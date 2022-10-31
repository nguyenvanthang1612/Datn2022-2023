<?php
/**
 * Created by PhpStorm.
 * User: kal
 * Date: 24/02/2020
 * Time: 13:08
 */

namespace Magenest\Ahamove\Setup;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Sales\Model\Order;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Sales\Setup\SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * UpgradeData constructor.
     * @param \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
     */
    public function __construct(\Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory)
    {
        $this->salesSetupFactory = $salesSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $installer]);
            $attrArray = ['api_order_id', 'api_shipping_status'];
            foreach ($attrArray as $attr){
                $salesSetup->addAttribute(Order::ENTITY, $attr, [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'visible' => false,
                    'nullable' => true
                ]);

                $installer->getConnection()->addColumn(
                    $installer->getTable('sales_order_grid'),
                    $attr,
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'Is Important'
                    ]
                );
            }

        }
        $installer->endSetup();
        // TODO: Implement upgrade() method.
    }
}