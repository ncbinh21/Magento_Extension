<?php

namespace Magenest\SagepayUS\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;

abstract class Card extends Action
{
    protected $_customerSession;
    protected $vaultFactory;
    protected $configHelper;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        \Magenest\SagepayUS\Model\VaultFactory $vaultFactory,
        \Magenest\SagepayUS\Helper\ConfigHelper $configHelper
    ) {
        $this->_customerSession = $customerSession;
        $this->vaultFactory = $vaultFactory;
        $this->configHelper = $configHelper;
        parent::__construct($context);
    }

    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get('Magento\Customer\Model\Url')->getLoginUrl();

        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }
}
