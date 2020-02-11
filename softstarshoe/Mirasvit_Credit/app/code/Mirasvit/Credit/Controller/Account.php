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



namespace Mirasvit\Credit\Controller;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

abstract class Account extends Action
{
    /**
     * @var \Mirasvit\Credit\Model\BalanceFactory
     */
    protected $balanceFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @param \Mirasvit\Credit\Model\BalanceFactory      $earningFactory
     * @param \Magento\Customer\Model\CustomerFactory    $customerFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Magento\Framework\Escaper                 $escaper
     * @param \Magento\Framework\App\Action\Context      $context
     */
    public function __construct(
        \Mirasvit\Credit\Model\BalanceFactory $earningFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->balanceFactory  = $earningFactory;
        $this->customerFactory = $customerFactory;
        $this->storeManager    = $storeManager;
        $this->customerSession = $customerSession;
        $this->context         = $context;
        $this->escaper         = $escaper;
        $this->resultFactory   = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->customerSession;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $action = $this->getRequest()->getActionName();

        if ($action != 'external' && $action != 'postexternal') {
            if (!$this->customerSession->authenticate()) {
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            }
        }

        return parent::dispatch($request);
    }
}
