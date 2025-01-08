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
class Save extends \Infrangible\BackendWidget\Controller\Backend\Object\Tab\Save
{
    use ProductCustomerPrice;
    use ProductCustomerPriceTab;

    protected function getObjectCreatedMessage(): string
    {
        return __('The customer price has been created.')->render();
    }

    protected function getObjectUpdatedMessage(): string
    {
        return __('The customer price has been saved.')->render();
    }
}
