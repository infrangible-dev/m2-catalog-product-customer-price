<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @method string getCustomerId()
 * @method void setCustomerId(string $customerId)
 * @method string getProductId()
 * @method void setProductId(string $productId)
 * @method float getPrice()
 * @method void setPrice(float $price)
 * @method int getDiscount()
 * @method void setDiscount(int $discount)
 * @method int getLimit()
 * @method void setLimit(int $limit)
 * @method int getPriority()
 * @method void setPriority(int $priority)
 * @method int getWebsiteId()
 * @method void setWebsiteId(int $websiteId)
 * @method int getUsed()
 * @method void setUsed(int $used)
 * @method int getActive()
 * @method void setActive(int $active)
 */
class ProductCustomerPrice extends AbstractModel
{
    protected $_eventPrefix = 'product_customer_price';

    protected function _construct(): void
    {
        $this->_init(ResourceModel\ProductCustomerPrice::class);
    }
}
