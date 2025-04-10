<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Model\Calculation;

use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\CatalogProductPriceCalculation\Helper\Data;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\Base;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\Prices\SimpleFactory;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\PricesInterface;
use Infrangible\CatalogProductPriceCalculation\Model\CalculationDataInterface;
use Infrangible\Core\Helper\Stores;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Quote\Model\Quote\Item;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ProductCustomerPrice extends Base implements CalculationDataInterface
{
    /** @var Session */
    protected $customerSession;

    /** @var \Magento\Checkout\Model\Session */
    protected $checkoutSession;

    /** @var Json */
    protected $json;

    /** @var Data */
    protected $priceCalculationHelper;

    /** @var Stores */
    protected $storeHelper;

    /** @var Variables */
    protected $variables;

    /** @var string */
    private $code;

    /** @var int */
    private $customerId;

    /** @var int */
    private $productId;

    /** @var float|null */
    private $price;

    /** @var int|null */
    private $discount;

    /** @var int|null */
    private $limit;

    /** @var int */
    private $websiteId;

    /** @var string */
    private $quoteItemOptionCode;

    public function __construct(
        SimpleFactory $pricesFactory,
        AmountFactory $amountFactory,
        Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        Json $json,
        Data $priceCalculationHelper,
        Stores $storeHelper,
        Variables $variables
    ) {
        parent::__construct(
            $pricesFactory,
            $amountFactory
        );

        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->json = $json;
        $this->priceCalculationHelper = $priceCalculationHelper;
        $this->storeHelper = $storeHelper;
        $this->variables = $variables;
    }

    public function getCode(): string
    {
        if ($this->code === null) {
            $this->code = sprintf(
                'pcp_%s',
                md5(
                    $this->json->encode(
                        [
                            'product_id' => $this->getProductId(),
                            'price'      => $this->getPrice(),
                            'discount'   => $this->getDiscount(),
                            'priority'   => $this->getPriority(),
                            'website_id' => $this->getWebsiteId()
                        ]
                    )
                )
            );
        }

        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(?int $discount): void
    {
        $this->discount = $discount;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function getWebsiteId(): int
    {
        return $this->websiteId;
    }

    public function setWebsiteId(int $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    public function getQuoteItemOptionCode(): string
    {
        return $this->quoteItemOptionCode;
    }

    public function setQuoteItemOptionCode(string $quoteItemOptionCode): void
    {
        $this->quoteItemOptionCode = $quoteItemOptionCode;
    }

    public function hasProductCalculation(Product $product): bool
    {
        return $product->getId() == $this->getProductId();
    }

    /**
     * @throws \Exception
     */
    public function getProductPrices(Product $product): PricesInterface
    {
        return $this->priceCalculationHelper->calculatePrices(
            $product,
            $this
        );
    }

    /**
     * @throws LocalizedException
     */
    public function isAvailableForProduct(): bool
    {
        if (! $this->customerSession->isLoggedIn()) {
            return false;
        }

        $customer = $this->customerSession->getCustomer();

        if ($customer->getId() != $this->getCustomerId()) {
            return false;
        }

        if (! $this->isWebsite()) {
            return false;
        }

        if (! $this->getLimit()) {
            return true;
        }

        $quote = $this->checkoutSession->getQuote();

        /** @var Item[] $quoteItems */
        $quoteItems = $quote->getItemsCollection()->getItems();

        $productQty = $this->getQuoteQty($quoteItems);

        return $productQty < $this->getLimit();
    }

    /**
     * @throws LocalizedException
     */
    private function isWebsite(): bool
    {
        if ($this->getWebsiteId() == 0) {
            return true;
        } else {
            $website = $this->storeHelper->getWebsite();
            $websiteId = $website->getId();

            if ($websiteId == $this->getWebsiteId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Item[] $calculatedItems
     *
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws \Exception
     */
    private function getQuoteQty(array $calculatedItems = []): float
    {
        $quote = $this->checkoutSession->getQuote();

        $quoteItems = $quote->getItemsCollection();

        $calculatedItemIds = [];

        foreach ($calculatedItems as $calculatedItem) {
            $calculatedItemIds[] = $calculatedItem->getId();
        }

        $productQty = 0;

        /** @var Item $quoteItem */
        foreach ($quoteItems as $quoteItem) {
            if (! in_array(
                $quoteItem->getId(),
                $calculatedItemIds
            )) {
                continue;
            }

            $productId = $this->variables->intValue($quoteItem->getProduct()->getId());

            if ($productId == $this->getProductId()) {
                $productQty += $quoteItem->getQty();
            }
        }

        return $productQty;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function hasProductQty(float $qty): bool
    {
        if (! $this->getLimit()) {
            return true;
        }

        $quote = $this->checkoutSession->getQuote();

        /** @var Item[] $quoteItems */
        $quoteItems = $quote->getItemsCollection()->getItems();

        $productQty = $this->getQuoteQty($quoteItems);

        return $productQty + $qty <= $this->getLimit();
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function isAvailableForQuoteItem(Item $item, array $calculatedItems): bool
    {
        if (! $this->customerSession->isLoggedIn()) {
            return false;
        }

        $customer = $this->customerSession->getCustomer();

        if ($customer->getId() != $this->getCustomerId()) {
            return false;
        }

        if (! $this->getLimit()) {
            return true;
        }

        $productQty = $this->getQuoteQty($calculatedItems);

        return $productQty + $item->getQty() <= $this->getLimit();
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function hasQuoteItemQty(Item $item, array $calculatedItems): bool
    {
        if (! $this->getLimit()) {
            return true;
        }

        $productQty = $this->getQuoteQty($calculatedItems);

        return $productQty + $item->getQty() <= $this->getLimit();
    }
}
