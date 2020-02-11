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



namespace Mirasvit\Credit\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\Credit\Helper\Message as MessageHelper;
use Mirasvit\Credit\Helper\Email as EmailHelper;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer;
use Magento\Framework\DB\Select;

/**
 * @method int getBalanceId()
 *
 * @method int getCustomerId()
 * @method $this setCustomerId(int $id)
 *
 * @method float getAmount()
 * @method $this setAmount(float $amount)
 *
 * @method bool getIsSubscribed()
 * @method $this setIsSubscribed(bool $status)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Balance extends AbstractModel
{
    /**
     * @var ResourceModel\Transaction\Collection
     */
    protected $transactionCollection;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var ResourceModel\Balance\CollectionFactory
     */
    protected $balanceCollectionFactory;

    /**
     * @var EmailHelper
     */
    protected $emailHelper;

    /**
     * @var MessageHelper
     */
    protected $messageHelper;

    /**
     * @param CustomerFactory                             $customerFactory
     * @param TransactionFactory                          $transactionFactory
     * @param ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     * @param ResourceModel\Balance\CollectionFactory     $balanceCollectionFactory
     * @param EmailHelper                                 $emailHelper
     * @param MessageHelper                               $messageHelper
     * @param Context                                     $context
     * @param Registry                                    $registry
     */
    public function __construct(
        CustomerFactory $customerFactory,
        TransactionFactory $transactionFactory,
        ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        ResourceModel\Balance\CollectionFactory $balanceCollectionFactory,
        EmailHelper $emailHelper,
        MessageHelper $messageHelper,
        Context $context,
        Registry $registry
    ) {
        $this->customerFactory = $customerFactory;
        $this->transactionFactory = $transactionFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->balanceCollectionFactory = $balanceCollectionFactory;
        $this->emailHelper = $emailHelper;
        $this->messageHelper = $messageHelper;

        parent::__construct($context, $registry);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Credit\Model\ResourceModel\Balance');
    }

    /**
     * @param bool $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /**
     * @return ResourceModel\Transaction\Collection
     */
    public function getTransactionCollection()
    {
        if (!$this->transactionCollection) {
            $this->transactionCollection = $this->transactionCollectionFactory->create()
                ->addFieldToFilter('balance_id', $this->getBalanceId());
        }

        return $this->transactionCollection;
    }

    /**
     * @return Customer|false
     */
    public function getCustomer()
    {
        if (!$this->getCustomerId()) {
            return false;
        }

        if ($this->customer === null) {
            $this->customer = $this->customerFactory->create()->load($this->getCustomerId());
        }

        return $this->customer;
    }

    /**
     * @param int|Customer $customer
     * @return Balance
     */
    public function loadByCustomer($customer)
    {
        if ($customer instanceof Customer) {
            $customer = $customer->getId();
        }

        // if is new customer
        if (!$customer) {
            return $this;
        }

        /** @var Balance $balance */
        $balance = $this->balanceCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customer)
            ->getFirstItem();

        if ($balance->getBalanceId()) {
            return $balance;
        }

        $this->setCustomerId($customer)
            ->setIsSubscribed(false)
            ->setAmount(0)
            ->save();

        return $this;
    }

    /**
     * @param float  $balanceDelta
     * @param string $action
     * @param string $message
     * @param bool   $forceEmail
     * @return $this|bool
     */
    public function addTransaction($balanceDelta, $action = null, $message = null, $forceEmail = false)
    {
        $balanceDelta = floatval($balanceDelta);

        if ($balanceDelta == 0) {
            return false;
        }

        if ($action == null) {
            $action = Transaction::ACTION_MANUAL;
        }

        if (is_array($message)) {
            $message = $this->messageHelper->createTransactionMessage($message);
        }

        $this->setAmount($this->getAmount() + $balanceDelta);

        /** @var Transaction $transaction */
        $transaction = $this->transactionFactory->create()
            ->setBalanceId($this->getId())
            ->setBalanceDelta($balanceDelta)
            ->setBalanceAmount($this->getAmount())
            ->setAction($action)
            ->setMessage($message)
            ->save();

        $this->save();
        if ($this->emailHelper->sendBalanceUpdateEmail($transaction, $forceEmail)) {
            $transaction->setIsNotified(true)
                ->save();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function recalculateBalance()
    {
        if (!$this->getId()) {
            return false;
        }
        $select = $this->transactionCollectionFactory->create()->getSelect();
        $select->reset(Select::ORDER)
            ->reset(Select::LIMIT_COUNT)
            ->reset(Select::LIMIT_OFFSET)
            ->reset(Select::COLUMNS)
            ->columns('SUM(balance_delta)')
            ->where('main_table.balance_id=?', $this->getId())
        ;

        $balance = abs($select->query()->fetchColumn());
        $this->setAmount($balance)->save();

        return true;
    }
}
