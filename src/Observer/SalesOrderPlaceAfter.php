<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Observer;

use FeWeDev\Base\Arrays;
use Infrangible\CatalogProductCustomerPrice\Model\ProductCustomerPriceFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class SalesOrderPlaceAfter implements ObserverInterface
{
    /** @var Arrays */
    protected $arrays;

    /** @var ProductCustomerPriceFactory */
    protected $productCustomerPriceFactory;

    /** @var \Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPriceFactory */
    protected $productCustomerPriceResourceFactory;

    public function __construct(
        Arrays $arrays,
        ProductCustomerPriceFactory $productCustomerPriceFactory,
        \Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPriceFactory $productCustomerPriceResourceFactory
    ) {
        $this->arrays = $arrays;
        $this->productCustomerPriceFactory = $productCustomerPriceFactory;
        $this->productCustomerPriceResourceFactory = $productCustomerPriceResourceFactory;
    }

    /**
     * @throws AlreadyExistsException
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getData('order');

        if ($order instanceof Order) {
            foreach ($order->getItems() as $item) {
                if ($item instanceof Item) {
                    $productOptions = $item->getProductOptions();

                    $priceCalculationId = $this->arrays->getValue(
                        $productOptions,
                        'price_calculation'
                    );

                    if ($priceCalculationId) {
                        $productCustomerPrice = $this->productCustomerPriceFactory->create();

                        $productCustomerPriceResource = $this->productCustomerPriceResourceFactory->create();

                        $productCustomerPriceResource->load(
                            $productCustomerPrice,
                            $priceCalculationId
                        );

                        if ($productCustomerPrice->getId()) {
                            $productCustomerPrice->setUsed($productCustomerPrice->getUsed() + 1);
                        }

                        $productCustomerPriceResource->save($productCustomerPrice);
                    }
                }
            }
        }
    }
}
