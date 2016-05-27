<?php

namespace Amazon\Login\Setup;

use Amazon\Login\Model\ResourceModel\CustomerLink;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $setup->getConnection()->addForeignKey(
                $setup->getFkName(CustomerLink::TABLE_NAME, 'customer_id', 'customer_entity', 'entity_id'),
                CustomerLink::TABLE_NAME,
                'customer_id',
                'customer_entity',
                'entity_id',
                AdapterInterface::FK_ACTION_CASCADE
            );
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $setup->getConnection()->addIndex(
                CustomerLink::TABLE_NAME,
                $setup->getIdxName(CustomerLink::TABLE_NAME, ['customer_id'], AdapterInterface::INDEX_TYPE_UNIQUE),
                ['customer_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            );
        }
    }
}
