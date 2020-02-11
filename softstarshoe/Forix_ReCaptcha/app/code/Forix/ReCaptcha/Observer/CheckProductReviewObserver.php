<?php
namespace Forix\ReCaptcha\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckProductReviewObserver implements ObserverInterface {
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
	 *
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $_urlManager;

	/**
	 * @var \Magento\Framework\App\Response\RedirectInterface
	 */
	protected $redirect;

	protected $_reviewSession;

	public function __construct(
		\Forix\ReCaptcha\Helper\Data $helper,
		\Magento\Framework\App\ActionFlag $actionFlag,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\UrlInterface $urlManager,
		\Magento\Framework\Session\Generic $reviewSession,
		\Magento\Framework\App\Response\RedirectInterface $redirect
	) {
		$this->_helper = $helper;
		$this->_actionFlag = $actionFlag;
		$this->messageManager = $messageManager;
		$this->_urlManager = $urlManager;
		$this->redirect = $redirect;
		$this->_reviewSession = $reviewSession;
	}

	/**
	 * Check Captcha On User Create Page
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer) {
		if (!$this->_helper->isEnabled())
			return $this;
		$formId = 'review_product';
		$controller = $observer->getControllerAction();
		if (in_array($formId, $this->_helper->getFormApplied())) {
			$grecaptchaResponse = $controller->getRequest()->getParam('g-recaptcha-response');
			if (!$grecaptchaResponse || !$this->_helper->verifyCaptcha($grecaptchaResponse)) {
				$this->messageManager->addError(__('Incorrect reCAPTCHA'));
				$this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
				$url = $this->redirect->getRedirectUrl();
				$controller->getResponse()->setRedirect($this->redirect->error($url));
			}
		}

		return $this;
	}
}
