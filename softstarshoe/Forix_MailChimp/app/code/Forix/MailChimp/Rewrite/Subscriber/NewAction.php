<?php
namespace Forix\MailChimp\Rewrite\Subscriber;
use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\Controller\ResultFactory;


class NewAction extends \Magento\Newsletter\Controller\Subscriber\NewAction
{
    /**
     * @var CustomerAccountManagement
     */
    protected $customerAccountManagement;

    protected $resultJsonFactory;
    private $_helperMailchimp;
    private $_api;
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl,
        CustomerAccountManagement $customerAccountManagement,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Ebizmarts\MailChimp\Helper\Data $helper
    ) {
        $this->customerAccountManagement = $customerAccountManagement;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct(
            $context,
            $subscriberFactory,
            $customerSession,
            $storeManager,
            $customerUrl,
            $customerAccountManagement
        );
        $this->_helperMailchimp = $helper;
        $this->_api             = $this->_helperMailchimp->getApi();
    }

    public function execute()
    {
        $message = [];
        $isAjax = $this->getRequest()->getPost('isajaxsubscribe');
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $email = (string)$this->getRequest()->getPost('email');

            $storeId = $this->_storeManager->getStore()->getId();

            $_isActive = $this->_helperMailchimp ->isMailChimpEnabled($storeId);
            $store_name = $this->getRequest()->getParam('store_name');

            if($_isActive && $store_name == "blog"){
                $_listMailchimp = $this->_helperMailchimp->getDefaultList();
                $md5HashEmail   = md5(strtolower($email));
                $api = $this->_api;
                $member = $api->lists->members->get($_listMailchimp, md5(strtolower($email)));
                if(isset($member['status']) && $member['status'] == 404){
                    $status = 'subscribed';
                    $result = $api->lists->members->addOrUpdate($this->_helperMailchimp->getDefaultList(), $md5HashEmail, null, $status, null, null, null, null, null, $email, $status);
                    if($isAjax) {
                        $message[] = 'success';
                        $message[] = __('Thank you for your subscription.');
                    }
                    else {
                        $this->messageManager->addSuccess(__('Thank you for your subscription.'));
                    }
                }
                if(isset($member['status']) && $member['status'] == "subscribed"){
                    if($isAjax) {
                        $message[] = 'error';
                        $message[] = __('This email address is already subscribed.');
                    }
                    else {
                        $this->messageManager->addNotice(__('This email address is already subscribed.'));
                    }
                }

            }else{
                //default magento
                try {
                    $this->validateEmailFormat($email);
                    $this->validateGuestSubscription();
                    $this->validateEmailAvailable($email);

                    $subscriber = $this->_subscriberFactory->create()->loadByEmail($email);
                    if ($subscriber->getId()
                        && $subscriber->getSubscriberStatus() == \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED
                    ) {
                        if($isAjax) {
                            $message[] = 'error';
                            $message[] = __('This email address is already subscribed.');
                        }
                        else {
                            throw new \Magento\Framework\Exception\LocalizedException(
                                __('This email address is already subscribed.')
                            );
                        }
                    }

                    $status = $this->_subscriberFactory->create()->subscribe($email);
                    if ($status == \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE) {
                        if($isAjax) {
                            $message[] = 'success';
                            $message[] = __('The confirmation request has been sent.');
                        }
                        else {
                            $this->messageManager->addSuccess(__('The confirmation request has been sent.'));
                        }
                    } else {
                        if($isAjax) {
                            $message[] = 'success';
                            $message[] = __('Thank you for your subscription.');
                        }
                        else {
                            $this->messageManager->addSuccess(__('Thank you for your subscription.'));
                        }
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    if($isAjax) {
                        $message[] = 'error';
                        $message[] = __($e->getMessage());
                    }
                    else {
                        $this->messageManager->addException(
                            $e,
                            __($e->getMessage())
                        );
                    }
                } catch (\Exception $e) {
                    if($isAjax) {
                        $message[] = 'error';
                        $message[] = __('Something went wrong with the subscription.');
                    }
                    else {
                        $this->messageManager->addException($e, __('Something went wrong with the subscription.'));
                    }
                }
            }
        }
        if($isAjax) {
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($message);
            return $resultJson;
        }
        else {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }
    }

    /**
     * Validates that the email address isn't being used by a different account.
     *
     * @param string $email
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function validateEmailAvailable($email)
    {
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        if ($this->_customerSession->getCustomerDataObject()->getEmail() !== $email
            && !$this->customerAccountManagement->isEmailAvailable($email, $websiteId)
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('This email is already subscribed.')
            );
        }
    }
}