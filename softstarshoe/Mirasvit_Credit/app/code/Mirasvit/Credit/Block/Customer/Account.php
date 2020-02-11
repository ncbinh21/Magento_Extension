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



namespace Mirasvit\Credit\Block\Customer;

use Magento\Framework\View\Element\Template;
use Mirasvit\Credit\Model\Config;

class Account extends Template
{
    /**
     * @var \Mirasvit\Credit\Model\BalanceFactory
     */
    protected $balanceFactory;

    /**
     * @var \Mirasvit\Credit\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Credit\Model\BalanceFactory                              $balanceFactory
     * @param \Mirasvit\Credit\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory                            $customerFactory
     * @param \Magento\Customer\Model\Session                                    $customerSession
     * @param Config                                                             $config
     * @param \Magento\Framework\View\Element\Template\Context                   $context
     */
    public function __construct(
        \Mirasvit\Credit\Model\BalanceFactory $balanceFactory,
        \Mirasvit\Credit\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        Config $config,
        \Magento\Framework\View\Element\Template\Context $context
    ) {
        $this->balanceFactory = $balanceFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->config = $config;
        $this->context = $context;

        parent::__construct($context);

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(__('Store Credit'));
        }
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function getCustomer()
    {
        return $this->customerFactory->create()->load($this->customerSession->getCustomerId());
    }

    /**
     * @return \Mirasvit\Credit\Model\Balance
     */
    public function getBalance()
    {
        return $this->balanceFactory->create()->loadByCustomer($this->getCustomer());
    }

    /**
     * @return \Mirasvit\Credit\Model\ResourceModel\Transaction\Collection|\Mirasvit\Credit\Model\Transaction[]
     */
    public function getTransactionCollection()
    {
        return $this->transactionCollectionFactory->create()
            ->addFieldToFilter('main_table.balance_id', $this->getBalance()->getId())
            ->setOrder('main_table.created_at', 'desc')
            ->setOrder('main_table.transaction_id', 'desc');
    }

    /**
     * @return string
     */
    public function getSend2FriendUrl()
    {
        return $this->context->getUrlBuilder()->getUrl('credit/account/send2friend');
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getSend2FriendFormData()
    {
        return new \Magento\Framework\DataObject((array)$this->customerSession->getSend2FriendFormData());
    }

    /**
     * @return bool
     */
    public function isSendBalanceUpdate()
    {
        return $this->config->isSendBalanceUpdate();
    }
}
