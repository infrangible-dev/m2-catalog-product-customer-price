<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Helper;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Data
{
    /** @var Cache */
    protected $cacheHelper;

    public function __construct(Cache $cacheHelper)
    {
        $this->cacheHelper = $cacheHelper;
    }

    /**
     * @throws \Exception
     */
    public function cleanProductCache()
    {
        $this->cacheHelper->cleanProductCache(
            'product_customer_price',
            'catalog_product_customer_price',
            'product_id',
            ['price', 'discount', 'priority']
        );
    }
}
