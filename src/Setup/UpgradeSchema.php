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

            if (! $connection->tableColumnExists($customerPricesTableName, 'priority')) {
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

            if (in_array($limitUsedIndexName, $connection->getIndexList($customerPricesTableName))) {
                $connection->dropIndex($customerPricesTableName, $limitUsedIndexName);
            }
        }

        $setup->endSetup();
    }
}
