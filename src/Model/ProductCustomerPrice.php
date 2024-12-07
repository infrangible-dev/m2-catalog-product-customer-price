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
 * @method setCustomerId(string $customerId)
 * @method string getProductId()
 * @method setProductId(string $productId)
 * @method float getPrice()
 * @method setPrice(float $price)
 * @method int getDiscount()
 * @method setDiscount(int $discount)
 * @method int getLimit()
 * @method void setLimit(int $limit)
 * @method int getPriority()
 * @method void setPriority(int $priority)
 * @method int getUsed()
 * @method setUsed(int $used)
 */
class ProductCustomerPrice extends AbstractModel
{
    protected $_eventPrefix = 'product_customer_price';

    protected function _construct(): void
    {
        $this->_init(ResourceModel\ProductCustomerPrice::class);
    }
}
