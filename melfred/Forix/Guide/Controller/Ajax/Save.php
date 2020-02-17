<?php

namespace Forix\Guide\Controller\Ajax;

use Magento\Framework\App\Action\Context;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Forix\Guide\Model\DownloadFactory
     */
    protected $downloadFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Save constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Forix\Guide\Model\DownloadFactory $downloadFactory
     * @param Context $context
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Forix\Guide\Model\DownloadFactory $downloadFactory,
        Context $context
    ) {
        $this->customerSession = $customerSession;
        $this->downloadFactory = $downloadFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if(isset($params)) {
            if($params['customer_id'] == 0) {
                $this->customerSession->setIsDownload('1');
            }
            $download = $this->downloadFactory->create();
            $download->setCustomerId($params['customer_id']);
            $download->setName($params['name']);
            $download->setCompany($params['company']);
            $download->setEmail(urldecode($params['email']));
            $download->save();
        }
    }
}