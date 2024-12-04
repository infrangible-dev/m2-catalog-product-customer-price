<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Block\Adminhtml\ProductCustomerPrice;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Grid extends \Infrangible\BackendWidget\Block\Grid
{
    protected function prepareCollection(AbstractDb $collection): void
    {
        if ($collection instanceof AbstractCollection) {
            $collection->getSelect()->where('name_attribute.attribute_code = "name"');
        }
    }

    /**
     * @throws \Exception
     */
    protected function prepareFields(): void
    {
        $this->addCustomerNameColumn(
            'customer_id',
            __('Customer')->render()
        );

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
            'used',
            __('Used')->render()
        );
    }

    /**
     * @return string[]
     */
    protected function getHiddenFieldNames(): array
    {
        return [];
    }
}
