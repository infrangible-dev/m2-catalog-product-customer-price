<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Observer;

use FeWeDev\Base\Variables;
use Infrangible\CatalogProductCustomerPrice\Model\Calculation\ProductCustomerPriceFactory;
use Infrangible\CatalogProductCustomerPrice\Model\ProductCustomerPrice;
use Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPrice\CollectionFactory;
use Infrangible\CatalogProductPriceCalculation\Model\Calculations;
use Infrangible\Core\Helper\Stores;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CatalogProductPriceCalculation implements ObserverInterface
{
    /** @var CollectionFactory */
    protected $productCustomerPriceCollectionFactory;

    /** @var ProductCustomerPriceFactory */
    protected $productCustomerPriceCalculationFactory;

    /** @var Variables */
    protected $variables;

    /** @var Stores */
    protected $storeHelper;

    public function __construct(
        ProductCustomerPriceFactory $productCustomerPriceCalculationFactory,
        CollectionFactory $productCustomerPriceCollectionFactory,
        Variables $variables,
        Stores $storeHelper
    ) {
        $this->productCustomerPriceCalculationFactory = $productCustomerPriceCalculationFactory;
        $this->productCustomerPriceCollectionFactory = $productCustomerPriceCollectionFactory;
        $this->variables = $variables;
        $this->storeHelper = $storeHelper;
    }

    /**
     * @throws \Exception
     */
    public function execute(Observer $observer): void
    {
        $website = $this->storeHelper->getWebsite();
        $websiteId = $website->getId();

        /** @var Calculations $calculations */
        $calculations = $observer->getData('calculations');

        $productCustomerPriceCollection = $this->productCustomerPriceCollectionFactory->create();
        $productCustomerPriceCollection->addUsableFilter();
        $productCustomerPriceCollection->addActiveFilter();
        $productCustomerPriceCollection->addPriorityOrder();
        $productCustomerPriceCollection->addWebsiteFilter($this->variables->intValue($websiteId));

        /** @var ProductCustomerPrice $productCustomerPrice */
        foreach ($productCustomerPriceCollection as $productCustomerPrice) {
            $productCustomerPriceCalculation = $this->productCustomerPriceCalculationFactory->create();

            $productCustomerPriceCalculation->setCustomerId(
                $this->variables->intValue($productCustomerPrice->getCustomerId())
            );

            $productCustomerPriceCalculation->setProductId(
                $this->variables->intValue($productCustomerPrice->getProductId())
            );

            if ($productCustomerPrice->getPrice()) {
                $productCustomerPriceCalculation->setPrice(floatval($productCustomerPrice->getPrice()));
            }

            if ($productCustomerPrice->getDiscount()) {
                $productCustomerPriceCalculation->setDiscount(
                    $this->variables->intValue($productCustomerPrice->getDiscount())
                );
            }

            $productCustomerPriceCalculation->setPriority(
                $this->variables->intValue($productCustomerPrice->getPriority())
            );

            if ($productCustomerPrice->getLimit()) {
                $productCustomerPriceCalculation->setLimit(
                    $productCustomerPrice->getLimit() - $productCustomerPrice->getUsed()
                );
            }

            $productCustomerPriceCalculation->setWebsiteId(
                $this->variables->intValue($productCustomerPrice->getWebsiteId())
            );

            $productCustomerPriceCalculation->setQuoteItemOptionCode($productCustomerPrice->getId());

            $calculations->addCalculation($productCustomerPriceCalculation);
        }
    }
}
