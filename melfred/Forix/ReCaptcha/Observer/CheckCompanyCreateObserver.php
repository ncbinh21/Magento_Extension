<?php
namespace Forix\ReCaptcha\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckCompanyCreateObserver implements ObserverInterface {
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
	 *
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $_urlManager;

	/**
	 * @var \Magento\Framework\App\Response\RedirectInterface
	 */
	protected $redirect;

	public function __construct(
		\Forix\ReCaptcha\Helper\Data $helper,
		\Magento\Framework\App\ActionFlag $actionFlag,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magento\Framework\UrlInterface $urlManager,
		\Magento\Framework\App\Response\RedirectInterface $redirect
	) {
		$this->_helper = $helper;
		$this->_actionFlag = $actionFlag;
		$this->messageManager = $messageManager;
		$this->_session = $session;
		$this->_urlManager = $urlManager;
		$this->redirect = $redirect;
	}

	/**
	 * Check Captcha On User Create Page
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer) {
		if(!$this->_helper->isEnabled())
			return $this;
		$formId = 'form-validate-company';
		$controller = $observer->getControllerAction();
		if (in_array($formId, $this->_helper->getFormApplied())) {
			$grecaptchaResponse = $controller->getRequest()->getParam('g-recaptcha-response');
			if (!$grecaptchaResponse || !$this->_helper->verifyCaptcha($grecaptchaResponse)) {
				$this->messageManager->addError(__('Incorrect reCAPTCHA'));
				$this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
				$url = $this->_urlManager->getUrl('*/*/create', ['_nosecret' => true]);
				$controller->getResponse()->setRedirect($this->redirect->error($url));
			}
		}

		return $this;
	}
}
