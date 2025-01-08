<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Block\Adminhtml\Customer\Edit;

use Infrangible\Core\Helper\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Data\FormFactory;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Tab extends Generic implements TabInterface
{
    /** @var Registry */
    protected $registryHelper;

    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        FormFactory $formFactory,
        Registry $registryHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $data
        );

        $this->registryHelper = $registryHelper;
    }

    public function getCustomerId(): ?int
    {
        return $this->registryHelper->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    public function getTabLabel(): string
    {
        return __('Customer Prices')->render();
    }

    public function getTabTitle(): string
    {
        return __('Customer Prices')->render();
    }

    public function getTabClass(): string
    {
        return '';
    }

    public function getTabUrl(): string
    {
        return $this->getUrl(
            'product_customer_price/tab',
            ['_current' => true]
        );
    }

    public function isAjaxLoaded(): bool
    {
        return true;
    }

    public function canShowTab(): bool
    {
        return $this->getCustomerId() !== null;
    }

    public function isHidden(): bool
    {
        return $this->getCustomerId() === null;
    }
}
