<?php

namespace Forix\Company\Rewrite\Model\Email;

use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Company\Model\Config\EmailTemplate as EmailTemplateConfig;
use Magento\Company\Model\Email\CustomerData;
use Magento\Company\Model\Email\Transporter;
use Magento\Customer\Api\CustomerNameGenerationInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Store\Model\ScopeInterface;

class Sender extends \Magento\Company\Model\Email\Sender
{
    /**
     * Email template for identity.
     */
    private $xmlPathRegisterEmailIdentity = 'customer/create_account/email_identity';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Company\Model\Email\Transporter
     */
    private $transporter;

    /**
     * @var \Magento\Company\Model\Config\EmailTemplate
     */
    private $emailTemplateConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Company\Model\Email\CustomerData
     */
    private $customerData;

    /**
     * @var \Magento\Customer\Api\CustomerNameGenerationInterface
     */
    private $customerViewHelper;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Transporter $transporter,
        CustomerNameGenerationInterface $customerViewHelper,
        CustomerData $customerData,
        EmailTemplateConfig $emailTemplateConfig,
        CompanyRepositoryInterface $companyRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ){
        $this->scopeConfig = $scopeConfig;
        $this->transporter = $transporter;
        $this->emailTemplateConfig = $emailTemplateConfig;
        $this->storeManager = $storeManager;
        $this->customerData = $customerData;
        $this->customerViewHelper = $customerViewHelper;
        $this->customerFactory = $customerFactory;
        parent::__construct($storeManager, $scopeConfig, $transporter, $customerViewHelper, $customerData, $emailTemplateConfig, $companyRepository);
    }

    /**
     * Notify company admin of company status change.
     *
     * @param CustomerInterface $customer
     * @param int $companyId
     * @param string $templatePath
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendCompanyStatusChangeNotificationEmail(CustomerInterface $customer, $companyId, $templatePath)
    {
        $storeId = $customer->getStoreId();
        if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($customer);
        }

        $copyTo = $this->emailTemplateConfig->getCompanyStatusChangeCopyTo(ScopeInterface::SCOPE_STORE);
        $copyMethod = $this->emailTemplateConfig->getCompanyStatusChangeCopyMethod(ScopeInterface::SCOPE_STORE);

        $sendTo = [];
        if ($copyTo && $copyMethod == 'copy') {
            $sendTo = explode(',', $copyTo);
        }
        array_unshift($sendTo, $customer->getEmail());
        $customerFactory = $this->customerFactory->create();
        $customerFactory->load($customer->getId());
        $customerEmailData = $this->customerData->getDataObjectByCustomer($customer, $companyId);
        $customerEmailData->setData('rp_token', $customerFactory->getRpToken());
        if ($customerEmailData !== null) {
            foreach ($sendTo as $recipient) {
                $this->sendEmailTemplate(
                    $recipient,
                    $this->customerViewHelper->getCustomerName($customer),
                    $this->scopeConfig->getValue($templatePath, ScopeInterface::SCOPE_STORE, $storeId),
                    $this->xmlPathRegisterEmailIdentity,
                    ['customer' => $customerEmailData],
                    $storeId,
                    ($copyTo && $copyMethod == 'bcc') ? explode(',', $copyTo) : []
                );
            }
        }
        return $this;
    }

    /**
     * Get either first store ID from a set website or the provided as default.
     *
     * @param CustomerInterface $customer
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getWebsiteStoreId(CustomerInterface $customer)
    {
        $defaultStoreId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        if ($customer->getWebsiteId() != 0) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }
        return $defaultStoreId;
    }

    /**
     * Send corresponding email template.
     *
     * @param string $customerEmail
     * @param string $customerName
     * @param string $templateId
     * @param string|array $sender configuration path of email identity
     * @param array $templateParams [optional]
     * @param int|null $storeId [optional]
     * @param array $bcc [optional]
     * @return void
     */
    private function sendEmailTemplate(
        $customerEmail,
        $customerName,
        $templateId,
        $sender,
        array $templateParams = [],
        $storeId = null,
        array $bcc = []
    ) {
        $from = $sender;
        if (is_string($sender)) {
            $from = $this->scopeConfig->getValue($sender, ScopeInterface::SCOPE_STORE, $storeId);
        }
        $this->transporter->sendMessage(
            $customerEmail,
            $customerName,
            $from,
            $templateId,
            $templateParams,
            $storeId,
            $bcc
        );
    }
}