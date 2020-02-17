<?php

namespace Forix\Customer\Plugin\Controller\Account;

class EditPost
{
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $_http;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Response\Http $http,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->_url = $url;
        $this->_http = $http;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Customer\Controller\Account\EditPost $subject
     */
    public function beforeExecute(
        \Magento\Customer\Controller\Account\EditPost $subject
    ) {
        $fullName = trim($subject->getRequest()->getParam('fullname'));
        $len = strlen($fullName);
        $pos = strpos($fullName, ' ');
        if ($pos == '') {
            $this->messageManager->addError('Please enter both your first name and last name.');
            return $this->_http->setRedirect($this->_url->getUrl('customer/account/index'));
        } else {
            $end = $len - $pos;
            $firstName = substr($fullName, 0, $pos);
            $lastname = substr($fullName, $pos, $end);
            $params = $subject->getRequest()->getParams();
            $params['firstname'] = trim($firstName);
            $params['lastname'] = trim($lastname);
            $subject->getRequest()->setParams($params);
        }
    }
}
