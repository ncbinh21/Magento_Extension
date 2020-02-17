<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 12/21/16
 * Time: 12:59
 */

namespace Magenest\SagepayUS\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Encryption\EncryptorInterface;

class ConfigHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_encryptor;
    protected $vaultFactory;
    const CLIENT_ID_CERT = "MSGsVA0QEmKdGj8sbDlDyY3n3j63S0HY";
    const CLIENT_KEY_CERT = "xsYDwH3g9bRQndjq";
    const API_ENDPOINT_CERT = "https://api-cert.sagepayments.com/";
    const CLIENT_ID_PROD = "0MsJ2l3W5KQWOh3G4DCx3l1TkpZSacg5";
    const CLIENT_KEY_PROD = "oLoURCj0ZbgkJZ8y";
    const API_ENDPOINT_PROD = "https://api.sagepayments.com/";

    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        \Magenest\SagepayUS\Model\VaultFactory $vaultFactory
    ) {
        parent::__construct($context);
        $this->_encryptor = $encryptor;
        $this->vaultFactory = $vaultFactory;
    }

    public function isActive()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_sagepayus/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isLoggerActive()
    {
        return true;
    }

    public function getMerchantId()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_sagepayus/merchant_id'
        );
    }

    public function getMerchantKey()
    {
        return $this->_encryptor->decrypt($this->scopeConfig->getValue(
            'payment/magenest_sagepayus/merchant_key'
        ));
    }

    public function getDeveloperId()
    {
        if($this->getEnvironment() == 'cert'){
            return self::CLIENT_ID_CERT;
        }else{
            return self::CLIENT_ID_PROD;
        }
    }

    public function getDeveloperKey()
    {
        if($this->getEnvironment() == 'cert'){
            return self::CLIENT_KEY_CERT;
        }else{
            return self::CLIENT_KEY_PROD;
        }
    }

    public function getPaymentMode()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_sagepayus/payment_mode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isTest()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_sagepayus/test',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getEnvironment()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_sagepayus/sage_environment',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPaymentAction()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_sagepayus/payment_action',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCanSaveCard()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_sagepayus/can_save_card',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSageBrowserDebug()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_sagepayus/sage_debug',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    ///------------------cal auth key---------------------

    public function getAuthKey($toBeHashed, $password, $salt, $iv)
    {
        $encryptHash = hash_pbkdf2("sha1", $password, $salt, 1500, 32, true);
        $encrypted = openssl_encrypt($toBeHashed, "aes-256-cbc", $encryptHash, 0, $iv);

        return $encrypted;
    }

    //---------------SAGE helper-------------------

    public function getNonces()
    {
        $iv = openssl_random_pseudo_bytes(16);
        $salt = base64_encode(bin2hex($iv));

        return [
            "iv" => $iv,
            "salt" => $salt
        ];
    }

    public function getHmac($toBeHashed, $privateKey = null)
    {
        if (!!$privateKey) {
            return base64_encode(hash_hmac('sha512', $toBeHashed, $privateKey, true));
        } else {
            return base64_encode(hash_hmac('sha512', $toBeHashed, $this->getDeveloperKey(), true));
        }
    }

    public function validateResponse($sageResp, $sageHash)
    {
        try {
            $hash = $this->getHmac($sageResp);
            if ($hash == $sageHash) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());

            return false;
        }
    }

    public function saveCard($vaultResponse, $cardInfo, $saveCardCheckbox = true){
        $cardInfo = json_decode($cardInfo, true);
        $customerSession = ObjectManager::getInstance()->create('Magento\Customer\Model\Session');
        if($this->getCanSaveCard() && $customerSession->isLoggedIn() && $saveCardCheckbox) {
            $customerId = $customerSession->getCustomerId();
            $dataAdd = [];
            foreach ($cardInfo as $k => $v){
                $dataAdd[$this->from_camel_case($k)] = $v;
            }
            if ($vaultResponse['status'] == "1") {
                $cardToken = $vaultResponse['data'];
                $vaultModel = $this->vaultFactory->create();
                $vaultModel->setData('customer_id', $customerId);
                $vaultModel->setData('card_id', $cardToken);
                $vaultModel->addData($dataAdd);
                $vaultModel->save();
                return true;
            }
        }
        return false;
    }

    private function from_camel_case($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}
