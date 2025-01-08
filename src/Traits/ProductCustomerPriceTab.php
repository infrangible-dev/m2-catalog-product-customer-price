<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Traits;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
trait ProductCustomerPriceTab
{
    protected function getParentObjectKey(): string
    {
        return 'customer_id';
    }

    protected function getParentObjectValueKey(): string
    {
        return 'id';
    }
}
