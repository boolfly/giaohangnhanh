<?php declare(strict_types=1);
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Giao hang nhanh
 */
namespace Boolfly\GiaoHangNhanh\Setup\Patch\Data;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Zend_Validate_Exception;

class AddressAttribute implements DataPatchInterface
{
    const DISTRICT = 'district';

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * AddressAttribute constructor.
     *
     * @param Config              $eavConfig
     * @param EavSetupFactory     $eavSetupFactory
     */
    public function __construct(
        Config $eavConfig,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavConfig = $eavConfig;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return AddressAttribute|void
     * @throws LocalizedException|Zend_Validate_Exception
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create();

        $eavSetup->addAttribute(AddressMetadataInterface::ENTITY_TYPE_ADDRESS, self::DISTRICT, [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'District',
            'input' => 'text',
            'required' => false,
            'sort_order' => 110,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => false,
            'visible' => true,
            'user_defined' => true,
            'system' =>  0,
            'is_html_allowed_on_front' => true,
            'visible_on_front' => true
        ]);

        $eavSetup->addAttributeToSet(
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
            AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS,
            1,
            self::DISTRICT
        );

        $district = $this->eavConfig->getAttribute(AddressMetadataInterface::ENTITY_TYPE_ADDRESS, self::DISTRICT);
        $district->setData(
            'used_in_forms',
            ['adminhtml_customer_address','customer_address_edit','customer_register_address']
        );
        $district->save();
    }
}