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
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

/**
 * @method int getBalanceId()
 * @method float getBalanceAmount()
 * @method float getBalanceDelta()
 * @method bool getIsNotified()
 * @method string getMessage()
 * @method string getAction()
 * @method string getCreatedAt()
 */
class Transaction extends AbstractModel
{
    const ACTION_MANUAL    = 'manual';
    const ACTION_EARNING   = 'earning';
    const ACTION_REFUNDED  = 'refunded';
    const ACTION_USED      = 'used';
    const ACTION_REFILL    = 'refill';
    const ACTION_PURCHASED = 'purchased';

    /**
     * @var Balance
     */
    protected $balance;

    /**
     * @var BalanceFactory
     */
    protected $balanceFactory;

    /**
     * @var MessageHelper
     */
    protected $messageHelper;

    /**
     * @param BalanceFactory $balanceFactory
     * @param MessageHelper  $messageHelper
     * @param Context        $context
     * @param Registry       $registry
     */
    public function __construct(
        BalanceFactory $balanceFactory,
        MessageHelper $messageHelper,
        Context $context,
        Registry $registry
    ) {
        $this->balanceFactory = $balanceFactory;
        $this->messageHelper = $messageHelper;

        parent::__construct($context, $registry);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Credit\Model\ResourceModel\Transaction');
    }

    /**
     * @return Balance|false
     */
    public function getBalance()
    {
        if (!$this->getBalanceId()) {
            return false;
        }

        if ($this->balance === null) {
            $this->balance = $this->balanceFactory->create()->load($this->getBalanceId());
        }

        return $this->balance;
    }

    /**
     * @return string
     */
    public function getBackendMessage()
    {
        return $this->messageHelper->getBackendTransactionMessage($this->getMessage());
    }

    /**
     * @return string
     */
    public function getFrontendMessage()
    {
        return $this->messageHelper->getFrontendTransactionMessage($this->getMessage());
    }

    /**
     * @return string
     */
    public function getEmailMessage()
    {
        return $this->messageHelper->getEmailTransactionMessage($this->getMessage());
    }
}
