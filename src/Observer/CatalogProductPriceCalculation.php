<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Observer;

use FeWeDev\Base\Variables;
use Infrangible\CatalogProductCustomerPrice\Model\Calculation\ProductCustomerPriceFactory;
use Infrangible\CatalogProductCustomerPrice\Model\ProductCustomerPrice;
use Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPrice\CollectionFactory;
use Infrangible\CatalogProductPriceCalculation\Model\Calculations;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
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

    public function __construct(
        ProductCustomerPriceFactory $productCustomerPriceCalculationFactory,
        CollectionFactory $productCustomerPriceCollectionFactory,
        Variables $variables
    ) {
        $this->productCustomerPriceCalculationFactory = $productCustomerPriceCalculationFactory;
        $this->productCustomerPriceCollectionFactory = $productCustomerPriceCollectionFactory;
        $this->variables = $variables;
    }

    /**
     * @throws \Exception
     */
    public function execute(Observer $observer): void
    {
        /** @var Calculations $calculations */
        $calculations = $observer->getData('calculations');

        $productCustomerPriceCollection = $this->productCustomerPriceCollectionFactory->create();
        $productCustomerPriceCollection->addUsableFilter();

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
            $productCustomerPriceCalculation->setQuoteItemOptionCode($productCustomerPrice->getId());

            $calculations->addCalculation($productCustomerPriceCalculation);
        }
    }
}
