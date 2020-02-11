<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Locale\CurrencyInterface;

class Credit extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Mirasvit\Credit\Model\BalanceFactory
     */
    protected $balanceFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $backendUrlManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;


    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    private $customerRepository;

    /**
     * @var CurrencyInterface
     */
    private $currency;

    public function __construct(
        \Mirasvit\Credit\Model\BalanceFactory $balanceFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Url $backendUrlManager,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Customer\Model\Customer $customerRepository,
        CurrencyInterface $currency,
        array $data = []
    ) {
        $this->balanceFactory = $balanceFactory;
        $this->date = $date;
        $this->formFactory = $formFactory;
        $this->backendUrlManager = $backendUrlManager;
        $this->registry = $registry;
        $this->context = $context;
        $this->customerRepository = $customerRepository;
        $this->currency = $currency;

        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTitle(__('Store Credit'));

        $this->setTemplate('customer/edit/tab/credit.phtml');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return $this->getTitle();
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTitle();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomer()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getAfter()
    {
        return 'tags';
    }

    /**
     * @return \Magento\Customer\Model\Customer|bool
     */
    public function getCustomer()
    {
        if ($customerId = $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)) {
            $customerData = $this->customerRepository->load($customerId);

            return $customerData;
        }

        return false;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        if ($this->getCustomer()) {
            $this->setChild(
                'grid',
                $this->getLayout()
                    ->createBlock('\Mirasvit\Credit\Block\Adminhtml\Customer\Edit\Tab\Credit\Grid', 'credit.grid')
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * @return float|string
     */
    public function getBalance()
    {
        $balance = $this->balanceFactory->create()->loadByCustomer($this->getCustomer());

        return $this->currency->getCurrency($this->context->getStoreManager()->getStore()->getBaseCurrencyCode())
            ->toCurrency($balance->getAmount());
    }

    /**
     * @return string
     */
    public function getLastChanged()
    {
        $balance = $this->balanceFactory->create()->loadByCustomer($this->getCustomer());
        $updatedAt = strtotime($balance->getUpdatedAt());

        return $updatedAt > 0 ? $this->date->date('M, d Y h:i A', $updatedAt) : '-';
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getIsSubscribed()
    {
        $balance = $this->balanceFactory->create()->loadByCustomer($this->getCustomer());

        return $balance->getIsSubscribed() ? __('Yes') : __('No');
    }

    /**
     * @return string|void
     */
    public function getStatusChangedDate()
    {
        $subscriber = $this->registry->registry('subscriber');
        if ($subscriber->getChangeStatusAt()) {
            return $this->formatDate(
                $subscriber->getChangeStatusAt(),
                \IntlDateFormatter::MEDIUM,
                true
            );
        }

        return;
    }

    /**
     * Tab should be loaded trough Ajax call.
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }
}
