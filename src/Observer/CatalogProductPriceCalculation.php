<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Observer;

use FeWeDev\Base\Variables;
use Infrangible\CatalogProductCustomerPrice\Model\Calculation\ProductCustomerPriceFactory;
use Infrangible\CatalogProductCustomerPrice\Model\ProductCustomerPrice;
use Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPrice\CollectionFactory;
use Infrangible\CatalogProductPriceCalculation\Model\Calculations;
use Infrangible\Core\Helper\Stores;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Adds the active customer prices of the website, whose limit is not used up, to the calculations.
 */
class CatalogProductPriceCalculation implements ObserverInterface
{
    /**
     * @param ProductCustomerPriceFactory $productCustomerPriceCalculationFactory
     * @param CollectionFactory $productCustomerPriceCollectionFactory
     * @param Variables $variables
     * @param Stores $storeHelper
     */
    public function __construct(
        protected ProductCustomerPriceFactory $productCustomerPriceCalculationFactory,
        protected CollectionFactory $productCustomerPriceCollectionFactory,
        protected Variables $variables,
        protected Stores $storeHelper
    ) {
    }

    /**
     * Adds the customer prices to the calculations of the event.
     *
     * @param Observer $observer
     *
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        $calculations = $observer->getData('calculations');

        if (! $calculations instanceof Calculations) {
            return;
        }

        $collection = $this->productCustomerPriceCollectionFactory->create();
        $collection->addUsableFilter();
        $collection->addActiveFilter();
        $collection->addPriorityOrder();
        $collection->addWebsiteFilter($this->variables->intValue($this->storeHelper->getWebsite()->getId()));

        /** @var ProductCustomerPrice $productCustomerPrice */
        foreach ($collection as $productCustomerPrice) {
            $calculation = $this->productCustomerPriceCalculationFactory->create();

            $calculation->setCustomerPriceId($this->variables->intValue($productCustomerPrice->getId()));
            $calculation->setCustomerId($this->variables->intValue($productCustomerPrice->getData('customer_id')));
            $calculation->setProductId($this->variables->intValue($productCustomerPrice->getData('product_id')));
            $calculation->setPriority($this->variables->intValue($productCustomerPrice->getData('priority')));
            $calculation->setWebsiteId($this->variables->intValue($productCustomerPrice->getData('website_id')));

            $price = $productCustomerPrice->getData('price');

            if (is_numeric($price)) {
                $calculation->setPrice((float)$price);
            }

            $discount = $this->getInt($productCustomerPrice->getData('discount'));

            if ($discount > 0) {
                $calculation->setDiscount($discount);
            }

            $limit = $this->getInt($productCustomerPrice->getData('limit'));

            if ($limit > 0) {
                $calculation->setLimit(max(0, $limit - $this->getInt($productCustomerPrice->getData('used'))));
            }

            $calculations->addCalculation($calculation);
        }
    }

    /**
     * Returns the value of a column that can be empty as number, 0 if it is empty.
     *
     * @param mixed $value
     *
     * @return int
     */
    private function getInt(mixed $value): int
    {
        return is_numeric($value) ? (int)$value : 0;
    }
}
