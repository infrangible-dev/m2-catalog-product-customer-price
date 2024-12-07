<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Observer;

use Infrangible\CatalogProductCustomerPrice\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ProductCustomerPriceDeleteCommitAfter implements ObserverInterface
{
    /** @var Data */
    protected $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @throws \Exception
     */
    public function execute(Observer $observer): void
    {
        $this->helper->cleanProductCache();
    }
}
