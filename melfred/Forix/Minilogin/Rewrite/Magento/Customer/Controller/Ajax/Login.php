<?php
/**
 * Created by PhpStorm.
 * User: nghia
 * Date: 08/04/2019
 * Time: 17:41
 */

namespace Forix\Minilogin\Rewrite\Magento\Customer\Controller\Ajax;

use Magento\Customer\CustomerData\SectionPoolInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;

class Login extends \Magento\Customer\Controller\Ajax\Login
{
    /**
     * @var SectionPoolInterface
     */
    protected $sectionPool;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Json\Helper\Data $helper,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        SectionPoolInterface $sectionPool
    ) {
        parent::__construct($context, $customerSession, $helper, $customerAccountManagement, $resultJsonFactory, $resultRawFactory);
        $this->sectionPool = $sectionPool;
    }
    public function execute()
    {
        $credentials = null;
        $httpBadRequestCode = 400;

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        try {
            $credentials = $this->helper->jsonDecode($this->getRequest()->getContent());
        } catch (\Exception $e) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }
        if (!$credentials || $this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        $response = [
            'errors' => false,
            'message' => __('Login successful.')
        ];
        try {
            $customer = $this->customerAccountManagement->authenticate(
                $credentials['username'],
                $credentials['password']
            );
            $this->customerSession->setCustomerDataAsLoggedIn($customer);
            $this->customerSession->regenerateId();
            $redirectRoute = $this->getAccountRedirect()->getRedirectCookie();
            if (!$this->getScopeConfig()->getValue('customer/startup/redirect_dashboard') && $redirectRoute) {
                $response['redirectUrl'] = $this->_redirect->success($redirectRoute);
                $this->getAccountRedirect()->clearRedirectCookie();
            }
        } catch (EmailNotConfirmedException $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        } catch (InvalidEmailOrPasswordException $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        } catch (LocalizedException $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $response = [
                'errors' => true,
                'message' => __('Invalid login or password.')
            ];
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}