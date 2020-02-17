<?php

namespace Forix\EmailToFriendPopup\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession

    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }
    /**
     * @param $value
     * @return mixed
     */
    public function getConfigValue($value) {
        return $this->scopeConfig->getValue($value);
    }

    /**
     * @return mixed
     */
    public function isAjaxAllowed()
    {
        return $this->getConfigValue('sendfriend/email/use_ajax');
    }

    public function allowPopup()
    {
        if (!$this->getConfigValue('sendfriend/email/allow_guest') && !$this->customerSession->isLoggedIn()) {
            return false;
        }
        if (!$this->getConfigValue('sendfriend/email/use_fancybox')) {
            return false;
        }
        return true;
    }
}