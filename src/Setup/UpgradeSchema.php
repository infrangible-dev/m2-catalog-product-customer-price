<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $customerPricesTableName = $setup->getTable('catalog_product_customer_price');

        if (version_compare(
            $context->getVersion(),
            '1.2.0',
            '<'
        )) {
            $connection = $setup->getConnection();

            if (! $connection->tableColumnExists(
                $customerPricesTableName,
                'priority'
            )) {
                $connection->addColumn(
                    $customerPricesTableName,
                    'priority',
                    [
                        'type'     => Table::TYPE_SMALLINT,
                        'length'   => 5,
                        'nullable' => false,
                        'default'  => 100,
                        'comment'  => 'Priority',
                        'after'    => 'limit'
                    ]
                );
            }

            $limitUsedIndexName = $setup->getIdxName(
                $customerPricesTableName,
                ['limit', 'used']
            );

            if (in_array(
                $limitUsedIndexName,
                $connection->getIndexList($customerPricesTableName)
            )) {
                $connection->dropIndex(
                    $customerPricesTableName,
                    $limitUsedIndexName
                );
            }
        }

        if (version_compare(
            $context->getVersion(),
            '1.5.0',
            '<'
        )) {
            $connection = $setup->getConnection();

            if (! $connection->tableColumnExists(
                $customerPricesTableName,
                'website_id'
            )) {
                $connection->addColumn(
                    $customerPricesTableName,
                    'website_id',
                    [
                        'type'     => Table::TYPE_SMALLINT,
                        'length'   => 5,
                        'nullable' => false,
                        'unsigned' => true,
                        'default'  => 0,
                        'comment'  => 'Website ID',
                        'after'    => 'priority'
                    ]
                );

                $websiteTableName = $connection->getTableName('store_website');

                $connection->addForeignKey(
                    $connection->getForeignKeyName(
                        $customerPricesTableName,
                        'website_id',
                        $websiteTableName,
                        'website_id'
                    ),
                    $customerPricesTableName,
                    'website_id',
                    $websiteTableName,
                    'website_id'
                );
            }
        }

        $setup->endSetup();
    }
}
