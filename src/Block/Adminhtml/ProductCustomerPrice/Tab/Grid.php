<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Block\Adminhtml\ProductCustomerPrice\Tab;

use Infrangible\BackendWidget\Block\Grid\Tab;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Grid extends Tab
{
    protected function prepareCollection(AbstractDb $collection): void
    {
    }

    /**
     * @throws \Exception
     */
    protected function prepareFields(): void
    {
        $this->addProductNameColumn(
            'product_id',
            __('Product')->render()
        );

        $this->addNumberColumn(
            'price',
            __('Price')->render()
        );

        $this->addNumberColumn(
            'discount',
            __('Discount')->render()
        );

        $this->addNumberColumn(
            'limit',
            __('Limit')->render()
        );

        $this->addNumberColumn(
            'priority',
            __('Priority')->render()
        );

        $this->addNumberColumn(
            'used',
            __('Used')->render()
        );

        $this->addYesNoColumn(
            'active',
            __('Active')->render()
        );
    }
}
