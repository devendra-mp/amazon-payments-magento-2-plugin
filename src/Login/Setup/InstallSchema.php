<?php

namespace Amazon\Login\Setup;

use Amazon\Login\Model\ResourceModel\CustomerLink;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $table = $setup->getConnection()->newTable(CustomerLink::TABLE_NAME);

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
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ]
            )
            ->addColumn(
                'amazon_id',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ]
            )
            ->addIndex(
                $setup->getIdxName(
                    CustomerLink::TABLE_NAME, ['customer_id', 'amazon_id'], AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['customer_id', 'amazon_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            );

        $setup->getConnection()->createTable($table);
    }
}
