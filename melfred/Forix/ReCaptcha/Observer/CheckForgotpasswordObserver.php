<?php
namespace Forix\ReCaptcha\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckForgotpasswordObserver implements ObserverInterface
{
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
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;


    /**
     * @param \Magento\Captcha\Helper\Data $helper
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     */
    public function __construct(
        \Forix\ReCaptcha\Helper\Data $helper,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect
    ) {
        $this->_helper = $helper;
        $this->_actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
    }

    /**
     * Check Captcha On Forgot Password Page
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->_helper->isEnabled())
            return $this;
        $formId = 'user_forgotpassword';
        $controller = $observer->getControllerAction();
        if (in_array($formId, $this->_helper->getFormApplied())) {
            $grecaptchaResponse = $controller->getRequest()->getParam('g-recaptcha-response');
            if (!$grecaptchaResponse || !$this->_helper->verifyCaptcha($grecaptchaResponse)) {
                $this->messageManager->addError(__('Incorrect reCAPTCHA'));
                $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                $this->redirect->redirect($controller->getResponse(), '*/*/forgotpassword');
            }
        }

        return $this;
    }
}
