<?php declare(strict_types=1);

namespace Boolfly\GiaoHangNhanh\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Eav setup factory
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * Init
     * @param EavSetupFactory $eavSetupFactory
     * @param Config $eavConfig
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttribute(
            'customer_address',
            'district',
            [
                'group' => 'General',
                'type' => 'varchar',
                'label' => 'District',
                'input' => 'select',
                'source' => 'Boolfly\GiaoHangNhanh\Model\Attribute\Source\District',
                'required' => false,
                'sort_order' => 110,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true
            ]
        );

        $customAttribute = $this->eavConfig->getAttribute('customer_address', 'district');

        $customAttribute->setData(
            'used_in_forms',
            ['adminhtml_customer_address','customer_address_edit','customer_register_address']
        );
        $customAttribute->save();

        $setup->endSetup();
    }
}
