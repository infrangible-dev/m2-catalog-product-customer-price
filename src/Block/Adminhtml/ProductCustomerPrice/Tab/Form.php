<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Block\Adminhtml\ProductCustomerPrice\Tab;

use Infrangible\BackendWidget\Block\Form\Tab;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Form extends Tab
{
    /**
     * @throws \Exception
     */
    protected function prepareFields(\Magento\Framework\Data\Form $form): void
    {
        $fieldSet = $form->addFieldset(
            'general',
            ['legend' => __('General')]
        );

        $this->addProductNameField(
            $fieldSet,
            'product_id',
            __('Product')->render(),
            true
        );

        $this->addPriceField(
            $fieldSet,
            'price',
            __('Price')->render()
        );

        $this->addDiscountField(
            $fieldSet,
            'discount',
            __('Discount')->render()
        );

        $this->addIntegerField(
            $fieldSet,
            'limit',
            __('Limit')->render()
        );

        $this->addIntegerField(
            $fieldSet,
            'priority',
            __('Priority')->render()
        );

        $this->addYesNoWithDefaultField(
            $fieldSet,
            'active',
            __('Active')->render(),
            1
        );
    }
}