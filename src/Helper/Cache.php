<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Helper;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Category;
use Infrangible\Core\Helper\Database;
use Infrangible\Core\Helper\Product;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\FlagManager;
use Magento\PageCache\Model\Cache\Type;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Cache
{
    /** @var FlagManager */
    protected $flagManager;

    /** @var Database */
    protected $databaseHelper;

    /** @var Arrays */
    protected $arrays;

    /** @var Product */
    protected $productHelper;

    /** @var Category */
    protected $categoryHelper;

    /** @var Variables */
    protected $variables;

    /** @var Block */
    protected $blockCache;

    /** @var Type */
    protected $fullPageCache;

    public function __construct(
        Database $databaseHelper,
        FlagManager $flagManager,
        Arrays $arrays,
        Product $productHelper,
        Category $categoryHelper,
        Variables $variables,
        Block $blockCache,
        Type $fullPageCache
    ) {
        $this->databaseHelper = $databaseHelper;
        $this->flagManager = $flagManager;
        $this->arrays = $arrays;
        $this->productHelper = $productHelper;
        $this->categoryHelper = $categoryHelper;
        $this->variables = $variables;
        $this->blockCache = $blockCache;
        $this->fullPageCache = $fullPageCache;
    }

    /**
     * @throws \Exception
     */
    public function cleanProductCache(string $flagName, string $tableName, string $productIdColumn, array $columns)
    {
        array_unshift($columns, $productIdColumn);

        $previousCalculationsData = $this->flagManager->getFlagData($flagName);

        $currentCalculationsQuery = $this->databaseHelper->select(
            $this->databaseHelper->getTableName($tableName),
            $columns
        );
        foreach ($columns as $column) {
            $currentCalculationsQuery->group($column);
            $currentCalculationsQuery->order($column);
        }

        $currentCalculationsData = $this->databaseHelper->fetchAssoc($currentCalculationsQuery);

        if (! $previousCalculationsData) {
            $previousCalculationsData = [];
        }

        $diff = $this->arrays->arrayDiffRecursive(
            $previousCalculationsData,
            $currentCalculationsData
        );

        $cacheTags = [];

        $productIds = array_keys($diff);

        foreach ($productIds as $productId) {
            $product = $this->productHelper->loadProduct($this->variables->intValue($productId));

            if ($product->getId()) {
                $cacheTags = array_merge(
                    $cacheTags,
                    $product->getIdentities()
                );

                $categoryIds = $product->getAvailableInCategories();

                foreach ($categoryIds as $categoryId) {
                    $category = $this->categoryHelper->loadCategory($this->variables->intValue($categoryId));

                    if ($category->getId()) {
                        $cacheTags = array_merge(
                            $cacheTags,
                            $category->getIdentities()
                        );
                    }
                }
            }
        }

        $cacheTags = array_unique($cacheTags);

        if (! empty($cacheTags)) {
            $this->blockCache->clean(
                \Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
                $cacheTags
            );

            $this->fullPageCache->clean(
                \Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
                $cacheTags
            );
        }

        $this->flagManager->saveFlag(
            'product_customer_price',
            $currentCalculationsData
        );
    }
}
