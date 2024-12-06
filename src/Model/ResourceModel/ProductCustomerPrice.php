<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ProductCustomerPrice extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('catalog_product_customer_price', 'id');
    }

    /**
     * @throws \Exception
     */
    protected function _beforeSave(AbstractModel $object): ProductCustomerPrice
    {
        parent::_beforeSave($object);

        if ($object->getData('price') == '' && $object->getData('discount') == '') {
            throw new \Exception('Either price or discount must be set');
        }

        if ($object->getData('price') != '' && $object->getData('discount') != '') {
            throw new \Exception('Either price or discount must be set');
        }

        if (! $object->getId()) {
            $object->setData(
                'created_at',
                gmdate('Y-m-d H:i:s')
            );
        }

        $object->setData(
            'updated_at',
            gmdate('Y-m-d H:i:s')
        );

        if ($object->getData('discount') == 0 || $object->getData('discount') == '') {
            $object->setData('discount');
        }

        if ($object->getData('price') == '' && $object->getData('discount') > 0) {
            $object->setData('price');
        }

        if ($object->getData('limit') == 0 || $object->getData('limit') == '') {
            $object->setData('limit');
        }

        return $this;
    }
}
