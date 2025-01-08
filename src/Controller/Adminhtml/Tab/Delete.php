<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Controller\Adminhtml\Tab;

use Infrangible\CatalogProductCustomerPrice\Traits\ProductCustomerPrice;
use Infrangible\CatalogProductCustomerPrice\Traits\ProductCustomerPriceTab;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Delete extends \Infrangible\BackendWidget\Controller\Backend\Object\Tab\Delete
{
    use ProductCustomerPrice;
    use ProductCustomerPriceTab;

    protected function getObjectDeletedMessage(): string
    {
        return __('The customer price has been deleted.')->render();
    }
}
