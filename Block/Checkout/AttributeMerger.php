<?php

namespace Boolfly\GiaoHangNhanh\Block\Checkout;

use Magento\Checkout\Block\Checkout\AttributeMerger as MageAttributeMerger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class AttributeMerger extends MageAttributeMerger
{
    /**
     * @param string $attributeCode
     * @param array $attributeConfig
     * @param array $additionalConfig
     * @param string $providerName
     * @param string $dataScopePrefix
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getFieldConfig(
        $attributeCode,
        array $attributeConfig,
        array $additionalConfig,
        $providerName,
        $dataScopePrefix
    ) {
        // street attribute is unique in terms of configuration, so it has its own configuration builder
        if (isset($attributeConfig['validation']['input_validation'])) {
            $validationRule = $attributeConfig['validation']['input_validation'];
            $attributeConfig['validation'][$this->inputValidationMap[$validationRule]] = true;
            unset($attributeConfig['validation']['input_validation']);
        }

        if ($attributeConfig['formElement'] == 'multiline') {
            return $this->getMultilineFieldConfig($attributeCode, $attributeConfig, $providerName, $dataScopePrefix);
        }

        $uiComponent = isset($this->formElementMap[$attributeConfig['formElement']])
            ? $this->formElementMap[$attributeConfig['formElement']]
            : 'Magento_Ui/js/form/element/abstract';
        $elementTemplate = isset($this->templateMap[$attributeConfig['formElement']])
            ? 'ui/form/element/' . $this->templateMap[$attributeConfig['formElement']]
            : 'ui/form/element/' . $attributeConfig['formElement'];

        $element = [
            'component' => isset($additionalConfig['component']) ? $additionalConfig['component'] : $uiComponent,
            'config' => $this->mergeConfigurationNode(
                'config',
                $additionalConfig,
                [
                    'config' => [
                        // customScope is used to group elements within a single
                        // form (e.g. they can be validated separately)
                        'customScope' => $dataScopePrefix,
                        'template' => 'ui/form/field',
                        'elementTmpl' => $elementTemplate,
                    ],
                ]
            ),
            'dataScope' => $dataScopePrefix . '.' . $attributeCode,
            'label' => $attributeConfig['label'],
            'provider' => $providerName,
            'sortOrder' => isset($additionalConfig['sortOrder'])
                ? $additionalConfig['sortOrder']
                : $attributeConfig['sortOrder'],
            'validation' => $this->mergeConfigurationNode('validation', $additionalConfig, $attributeConfig),
            'options' => $this->getFieldOptions($attributeCode, $attributeConfig),
            'filterBy' => isset($additionalConfig['filterBy']) ? $additionalConfig['filterBy'] : null,
            'customEntry' => isset($additionalConfig['customEntry']) ? $additionalConfig['customEntry'] : null,
            'visible' => isset($additionalConfig['visible']) ? $additionalConfig['visible'] : true,
        ];

        if ($attributeCode === 'region_id' || $attributeCode === 'country_id' || $attributeCode === 'district') {
            unset($element['options']);
            $element['deps'] = [$providerName];
            $element['imports'] = [
                'initialOptions' => 'index = ' . $providerName . ':dictionaries.' . $attributeCode,
                'setOptions' => 'index = ' . $providerName . ':dictionaries.' . $attributeCode
            ];
        }

        if (isset($attributeConfig['value']) && $attributeConfig['value'] != null) {
            $element['value'] = $attributeConfig['value'];
        } elseif (isset($attributeConfig['default']) && $attributeConfig['default'] != null) {
            $element['value'] = $attributeConfig['default'];
        } else {
            $defaultValue = $this->getDefaultValue($attributeCode);
            if (null !== $defaultValue) {
                $element['value'] = $defaultValue;
            }
        }
        return $element;
    }
}