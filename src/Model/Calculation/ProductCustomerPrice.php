<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Model\Calculation;

use FeWeDev\Base\Json;
use Infrangible\CatalogProductPriceCalculation\Helper\Data;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\Base;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\Prices\SimpleFactory;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\PricesInterface;
use Infrangible\CatalogProductPriceCalculation\Model\CalculationDataInterface;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\Amount\AmountFactory;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ProductCustomerPrice extends Base implements CalculationDataInterface
{
    /** @var Session */
    protected $customerSession;

    /** @var Data */
    protected $priceCalculationHelper;

    /** @var Json */
    protected $json;

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

    /** @var string */
    private $quoteItemOptionCode;

    public function __construct(
        SimpleFactory $pricesFactory,
        AmountFactory $amountFactory,
        Session $customerSession,
        Json $json,
        Data $priceCalculationHelper
    ) {
        parent::__construct(
            $pricesFactory,
            $amountFactory
        );

        $this->customerSession = $customerSession;
        $this->json = $json;
        $this->priceCalculationHelper = $priceCalculationHelper;
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
                            'priority'   => $this->getPriority()
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

    public function isActive(): bool
    {
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();

            if ($customer->getId() == $this->getCustomerId()) {
                return true;
            }
        }

        return false;
    }
}
