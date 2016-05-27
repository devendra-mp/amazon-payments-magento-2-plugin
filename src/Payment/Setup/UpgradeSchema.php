<?php

namespace Amazon\Payment\Setup;

use Amazon\Payment\Api\Data\PendingCaptureInterface;
use Amazon\Payment\Model\ResourceModel\OrderLink;
use Amazon\Payment\Model\ResourceModel\PendingCapture;
use Amazon\Payment\Model\ResourceModel\QuoteLink;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * @param EavSetup $eavSetup
     */
    public function __construct(EavSetup $eavSetup)
    {
        $this->eavSetup = $eavSetup;
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $linkTables = [
                'quote_id' => QuoteLink::TABLE_NAME,
                'order_id' => OrderLink::TABLE_NAME
            ];

            foreach ($linkTables as $fieldName => $tableName) {
                $table = $setup->getConnection()->newTable($tableName);

                $table
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'unsigned' => true,
                            'primary'  => true
                        ]
                    )
                    ->addColumn(
                        $fieldName,
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false
                        ]
                    )
                    ->addColumn(
                        'amazon_order_reference_id',
                        Table::TYPE_TEXT,
                        255,
                        [
                            'nullable' => false
                        ]
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName, [$fieldName], AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        [$fieldName],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    );

                $setup->getConnection()->createTable($table);
            }
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {

            $setup->getConnection()->addColumn(
                $setup->getTable(QuoteLink::TABLE_NAME),
                'sandbox_simulation_reference',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Sandbox simulation reference'
                ]
            );

        }

        if (version_compare($context->getVersion(), '1.3.0', '<')) {

            $setup->getConnection()->addColumn(
                $setup->getTable(QuoteLink::TABLE_NAME),
                'confirmed',
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0,
                    'type' => Table::TYPE_SMALLINT,
                    'comment' => 'Quote confirmed with Amazon'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $table = $setup->getConnection()->newTable(PendingCapture::TABLE_NAME);

            $table
                ->addColumn(
                    PendingCaptureInterface::ID,
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'primary'  => true
                    ]
                )
                ->addColumn(
                    PendingCaptureInterface::CAPTURE_ID,
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ]
                )
                ->addColumn(
                    PendingCaptureInterface::CREATED_AT,
                    Table::TYPE_DATETIME,
                    null,
                    [
                        'nullable' => false
                    ]
                )
                ->addIndex(
                    $setup->getIdxName(
                        PendingCapture::TABLE_NAME, [PendingCaptureInterface::CAPTURE_ID], AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    [PendingCaptureInterface::CAPTURE_ID],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                );

            $setup->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->upgradeAddressStreetMultiline();
        }

        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            $setup->getConnection()->addForeignKey(
                $setup->getFkName(QuoteLink::TABLE_NAME, 'quote_id', 'quote', 'entity_id'),
                QuoteLink::TABLE_NAME,
                'quote_id',
                'quote',
                'entity_id',
                AdapterInterface::FK_ACTION_CASCADE
            );

            $setup->getConnection()->addForeignKey(
                $setup->getFkName(OrderLink::TABLE_NAME, 'order_id', 'sales_order', 'entity_id'),
                OrderLink::TABLE_NAME,
                'order_id',
                'sales_order',
                'entity_id',
                AdapterInterface::FK_ACTION_CASCADE
            );
        }
    }

    /**
     * @throws LocalizedException
     * @return void
     */
    private function upgradeAddressStreetMultiline()
    {
        $row = $this->eavSetup->getAttribute('customer_address', 'street', 'multiline_count');

        if ($row === false || !is_numeric($row)) {
            throw new LocalizedException(__('Could not find the "multiline_count" config of the "street" Customer address attribute.'));
        }

        if ($row < 3) {
            $this->eavSetup->updateAttribute('customer_address', 'street', 'multiline_count', 3);
        }
    }
}
