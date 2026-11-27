<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Observer;

use Exception;
use FeWeDev\Base\Arrays;
use Infrangible\CatalogProductCustomerPrice\Helper\Data as Helper;
use Infrangible\CatalogProductCustomerPrice\Model\ProductCustomerPriceFactory;
use Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPriceFactory as ResourceFactory;
use Infrangible\CatalogProductPriceCalculation\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Adds the ordered quantities of the products with a customer price to the used quantity of the price.
 */
class SalesOrderPlaceAfter implements ObserverInterface
{
    /**
     * @param Arrays $arrays
     * @param Helper $helper
     * @param ProductCustomerPriceFactory $productCustomerPriceFactory
     * @param ResourceFactory $productCustomerPriceResourceFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected Arrays $arrays,
        protected Helper $helper,
        protected ProductCustomerPriceFactory $productCustomerPriceFactory,
        protected ResourceFactory $productCustomerPriceResourceFactory,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * Adds the ordered quantities to the customer prices of the order items.
     *
     * The order is placed already: a failure is logged instead of failing the order.
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getData('order');

        if (! $order instanceof Order) {
            return;
        }

        $orderedQtys = [];

        foreach ($order->getAllVisibleItems() as $item) {
            if (! $item instanceof Item) {
                continue;
            }

            $customerPriceId = $this->helper->getCustomerPriceIdByOptionCode(
                $this->arrays->getValue((array)$item->getProductOptions(), Data::QUOTE_ITEM_OPTION_CODE)
            );

            if ($customerPriceId !== null) {
                $orderedQtys[ $customerPriceId ] =
                    ($orderedQtys[ $customerPriceId ] ?? 0) + (int)ceil((float)$item->getQtyOrdered());
            }
        }

        foreach ($orderedQtys as $customerPriceId => $orderedQty) {
            try {
                $this->addUsedQty($customerPriceId, $orderedQty);
            } catch (Throwable $exception) {
                $this->logger->error($exception);
            }
        }
    }

    /**
     * Adds the quantity to the used quantity of the customer price, if it exists.
     *
     * @param int $customerPriceId
     * @param int $qty
     *
     * @return void
     * @throws Exception
     */
    private function addUsedQty(int $customerPriceId, int $qty): void
    {
        $productCustomerPrice = $this->productCustomerPriceFactory->create();
        $productCustomerPriceResource = $this->productCustomerPriceResourceFactory->create();

        $productCustomerPriceResource->load($productCustomerPrice, $customerPriceId);

        if (! $productCustomerPrice->getId()) {
            return;
        }

        $used = $productCustomerPrice->getData('used');

        $productCustomerPrice->setData('used', (is_numeric($used) ? (int)$used : 0) + $qty);

        $productCustomerPriceResource->save($productCustomerPrice);
    }
}
