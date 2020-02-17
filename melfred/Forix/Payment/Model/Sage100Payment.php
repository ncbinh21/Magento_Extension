<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 16/11/2018
 * Time: 11:53
 */

namespace Forix\Payment\Model;

use \Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\DataObject;
use \Magento\Sales\Api\Data\OrderPaymentInterface;
class Sage100Payment extends \Magento\Payment\Model\Method\Cc
{
    const CODE = 'sage100_service';
    const PAYMENT_TYPE = 'VISA';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::CODE;

    protected $_sage100Factory;

    protected $_sage100;

    protected $_canAuthorize = true;

    protected $_paymentType = self::PAYMENT_TYPE;
    protected $_paymentHelper;
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;
    /**
     * @var \Forix\Payment\Model\ResourceModel\CcGuId\CollectionFactory
     */
    protected $_ccguidCollectionFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Forix\Payment\Model\Sage100Factory $sage100Factory,
        \Forix\Payment\Helper\PaymentHelper $paymentHelper,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Forix\Payment\Model\ResourceModel\CcGuId\CollectionFactory $ccguidCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $moduleList, $localeDate, $resource, $resourceCollection, $data);
        $this->_sage100Factory = $sage100Factory;
        $this->_paymentHelper = $paymentHelper;
        $this->_encryptor = $encryptor;
        $this->_ccguidCollectionFactory = $ccguidCollectionFactory;
    }

    public function getSage100Instance(){
        if(is_null($this->_sage100)){
            $this->_sage100 = $this->_sage100Factory->create();
        }
        return $this->_sage100;
    }

    /**
     * @param \Magento\Framework\DataObject $data
     * @return \Magento\Payment\Model\Method\AbstractMethod|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        /**
         * Add Credit To Vault
         */
        $additionalData = $data->getAdditionalData();
        if (!is_object($additionalData)) {
            $additionalData = new DataObject($additionalData ?: []);
        }

        $info = $this->getInfoInstance();
        $info->addData(
            [
                'payment_type' => $this->_paymentType,
                'cc_cid' => $additionalData->getCcCid(),
                OrderPaymentInterface::CC_TYPE => $additionalData->getCcType(),
                OrderPaymentInterface::CC_LAST_4 => substr($additionalData->getCcNumber(), -4),
                'cc_number' => $additionalData->getCcNumber(),
                OrderPaymentInterface::CC_EXP_MONTH => $additionalData->getCcExpMonth(),
                OrderPaymentInterface::CC_EXP_YEAR => $additionalData->getCcExpYear(),
            ]
        );
        return $this;
    }


    /**
     * @return bool
     * @api
     */
    public function hasVerification()
    {
        return true;
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {

        parent::authorize($payment, $amount);
        /**
         * @var $order \Magento\Sales\Model\Order
         * @var $info \Magento\Sales\Model\Order\Payment
         */
        $info = $this->getInfoInstance();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $order = $payment->getOrder();
        if(!$order->getSalesOrderNo()) {
            $logger->info('Error authorize');
            throw new LocalizedException(__('We cannot register Sales Order No, Please try to place the order again.'));
        }

        //$logger->info(print_r($info->getAdditionalData(),1));
        //$logger->info(print_r($info->getData(),1));
        /**
         * Authorize Card In Vault
         */
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        if(is_null($shippingAddress)){
            $shippingAddress = $billingAddress;
        }
        $customerNo = $this->_paymentHelper->getCustomerNo($order);
        if(!$customerNo){
            throw new LocalizedException(__('We cannot register Customer No, Please try to place the order again.'));
        }
        $order->setCustomerNo($customerNo);
        $creditCardData = sprintf("%s|%02d%02d", $info->getCcNumber(), $info->getCcExpMonth(), substr($info->getCcExpYear(), -2));
        $encryptedCreditCardData = $this->_encryptor->getHash($creditCardData);
        /**
         * @var $ccguidCollection \Forix\Payment\Model\ResourceModel\CcGuId\Collection
         */
        $ccguidCollection = $this->_ccguidCollectionFactory->create();
        $ccguidCollection->addFieldToFilter('cc_num',$encryptedCreditCardData);
        if($ccguidCollection->getSize() > 0){
            $result = $ccguidCollection->getFirstItem()->getCcGuid();
        }else{
            $result = $this->getSage100Instance()->addCreditCardToVault($this->_paymentType, $creditCardData);
            $ccguidCollection->getResource()->getConnection()->insert(
                $ccguidCollection->getMainTable(),
                [
                    'cc_num' => $encryptedCreditCardData,
                    'cc_guid' => $result
                ]
            );
        }
        if(!$result){
            throw new LocalizedException(__('We can\'t authorize card, Please try to place the order again.'));
        }
        $info->setAdditionalInformation('CreditCardGUID', $result); //Validation call double

        $preAuthorizationData = new \Magento\Framework\DataObject([
            'Address' => implode(' ',$billingAddress->getStreet()),
            'Amount' => $amount,
            'City' => $billingAddress->getCity(),
            'Country' => $billingAddress->getCountryId(),
            'CreditCardCVV' => $payment->getData('cc_cid'),
            'CreditCardGUID' => $result,
            'CustomerNo' => $customerNo,
            'EmailAddress' => $order->getCustomerEmail(),
            'FaxNo' => $billingAddress->getFax(),
            'Name' => $order->getCustomerName(),
            'SalesOrderNo' => $order->getSalesOrderNo(), //Neu order khong co thong tin nay <=> Order nay khong nen duoc push ve Sage100
            'ShippingAddress' => implode(' ',$shippingAddress->getStreet()),
            'ShippingAmount' => $amount,
            'ShippingCity' => $shippingAddress->getCity(),
            'ShippingCountry' => $shippingAddress->getCountryId(),
            'ShippingName' => $shippingAddress->getName(),
            'ShippingState' => $shippingAddress->getRegion(),
            'ShippingZipCode' => $shippingAddress->getPostcode(),
            'State' => $billingAddress->getRegion(),
            'TaxAmount' => 0,//$order->getTaxAmount(),
            'TelephoneNo' => $billingAddress->getTelephone(),
            'ZipCode' => $billingAddress->getPostcode()
        ]);

        $result = $this->getSage100Instance()->preAuthorizeCreditCard($this->_paymentType, $preAuthorizationData);
        if($result) {
            $this->getInfoInstance()->setAdditionalInformation('approval_indicator' , $result->ApprovalIndicator);
            $this->getInfoInstance()->setAdditionalInformation('authorization_date_time' , $result->AuthorizationDateTime);
            $this->getInfoInstance()->setAdditionalInformation('credit_card_authorization_no' , $result->CreditCardAuthorizationNo);
            $this->getInfoInstance()->setAdditionalInformation('credit_card_transaction_id' , $result->CreditCardTransactionID);
            $this->getInfoInstance()->setAdditionalInformation('credit_card_message' , $result->Message);
            if(false === $this->getSage100Instance()->createSalesOrder($order)){
                throw new LocalizedException(__('We cannot save order, Please try to place the order again.'));
            }
        }else{
            throw new LocalizedException(__('We can\'t authorize card, Please try to place the order again.'));
        }
        return $this;
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return \Magento\Payment\Model\Method\AbstractMethod::isAvailable($quote);
    }
}