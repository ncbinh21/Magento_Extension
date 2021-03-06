<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\ReCaptcha\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\TestFramework\Inspection\Exception;

class CheckUserAjaxLoginObserver implements ObserverInterface {
	/**
	 * @var \Forix\ReCaptcha\Helper\Data
	 */
	protected $_helper;

	/**
	 * @var \Magento\Framework\App\ActionFlag
	 */
	protected $_actionFlag;

	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;

	/**
	 * @var \Magento\Framework\Session\SessionManagerInterface
	 */
	protected $_session;


	/**
	 * Customer data
	 *
	 * @var \Magento\Customer\Model\Url
	 */
	protected $_customerUrl;

	/**
	 * @param \Forix\ReCaptcha\Helper\Data $helper
	 * @param \Magento\Framework\App\ActionFlag $actionFlag
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Magento\Framework\Session\SessionManagerInterface $session
	 * @param \Magento\Customer\Model\Url $customerUrl
	 */
	public function __construct(
		\Forix\ReCaptcha\Helper\Data $helper,
		\Magento\Framework\App\ActionFlag $actionFlag,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magento\Customer\Model\Url $customerUrl
	) {
		$this->_helper = $helper;
		$this->_actionFlag = $actionFlag;
		$this->messageManager = $messageManager;
		$this->_session = $session;
		$this->_customerUrl = $customerUrl;
	}

	/**
	 * Check Captcha On User Login Page
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer) {
		if(!$this->_helper->isEnabled())
			return $this;
		$formId = 'mini_login';
		$controller = $observer->getControllerAction();
		if (in_array($formId, $this->_helper->getFormApplied())) {
			$grecaptchaResponse = $controller->getRequest()->getParam('g-recaptcha-response');
			if (!$grecaptchaResponse)
				throw new Exception("Incorrect Captcha");
			if (!$this->_helper->verifyCaptcha($grecaptchaResponse)) {
				throw new Exception("Incorrect Captcha");
			}
		}
		return $this;
	}
}
