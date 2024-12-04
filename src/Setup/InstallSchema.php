<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @throws \Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        $customerPricesTableName = $setup->getTable('catalog_product_customer_price');

        if (! $setup->tableExists($customerPricesTableName)) {
            $customerEntityTableName = $setup->getTable('customer_entity');
            $productEntityTableName = $setup->getTable('catalog_product_entity');

            $customerPricesTable = $connection->newTable($customerPricesTableName);

            $customerPricesTable->addColumn(
                'id',
                Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            );
            $customerPricesTable->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false]
            );
            $customerPricesTable->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false]
            );
            $customerPricesTable->addColumn(
                'price',
                Table::TYPE_DECIMAL,
                [20, 2],
                ['unsigned' => false, 'nullable' => true]
            );
            $customerPricesTable->addColumn(
                'discount',
                Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => true]
            );
            $customerPricesTable->addColumn(
                'limit',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => true]
            );
            $customerPricesTable->addColumn(
                'used',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => true, 'default' => 0]
            );
            $customerPricesTable->addColumn(
                'active',
                Table::TYPE_SMALLINT,
                1,
                ['nullable' => false, 'unsigned' => true, 'default' => 0]
            );
            $customerPricesTable->addColumn(
                'created_at',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false, 'default' => '0000-00-00 00:00:00']
            );
            $customerPricesTable->addColumn(
                'updated_at',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false, 'default' => '0000-00-00 00:00:00']
            );

            $customerPricesTable->addForeignKey(
                $setup->getFkName(
                    $customerPricesTableName,
                    'customer_id',
                    $customerEntityTableName,
                    'entity_id'
                ),
                'customer_id',
                $customerEntityTableName,
                'entity_id',
                Table::ACTION_CASCADE
            );

            $customerPricesTable->addForeignKey(
                $setup->getFkName(
                    $customerPricesTableName,
                    'product_id',
                    $productEntityTableName,
                    'entity_id'
                ),
                'product_id',
                $productEntityTableName,
                'entity_id',
                Table::ACTION_CASCADE
            );

            $customerPricesTable->addIndex(
                $setup->getIdxName(
                    $customerPricesTableName,
                    ['limit', 'used']
                ),
                ['limit', 'used']
            );

            $connection->createTable($customerPricesTable);
        }

        $setup->endSetup();
    }
}
