<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Controller\Adminhtml\Tab;

use Infrangible\CatalogProductCustomerPrice\Traits\ProductCustomerPrice;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Add extends \Infrangible\BackendWidget\Controller\Backend\Object\Tab\Add
{
    use ProductCustomerPrice;
}
