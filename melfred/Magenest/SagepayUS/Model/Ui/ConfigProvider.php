<?php
/**
 * Created by Magenest.
 * Author: Joel
 * Date: 18/10/2016
 * Time: 10:29
 */

namespace Magenest\SagepayUS\Model\Ui;

use Magenest\SagepayUS\Model\SagepayUSPayment;
use Magento\Framework\App\ObjectManager;

class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    const CODE = SagepayUSPayment::CODE;

    protected $_config;
    protected $_storeManager;
    protected $vaultFactory;

    public function __construct(
        \Magenest\SagepayUS\Helper\ConfigHelper $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magenest\SagepayUS\Model\VaultFactory $vaultFactory
    ) {
        $this->_config = $config;
        $this->_storeManager = $storeManager;
        $this->vaultFactory = $vaultFactory;
    }

    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'payment_mode' => $this->_config->getPaymentMode(),
                    'is_test' => (bool)$this->_config->isTest(),
                    'card_data' => json_encode($this->getCardData()),
                    'is_save_card' => (bool)$this->_config->getCanSaveCard()
                ]
            ]
        ];
    }

    private function getCardData()
    {
        $objectManager = ObjectManager::getInstance();
        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        $dataReturn = [];
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getCustomerId();
            $cardCollection = $this->vaultFactory->create()->getCollection();
            $cardCollection->addFieldToFilter("customer_id", $customerId);
            foreach ($cardCollection as $card){
                $dataReturn[] = [
                    'id' => $card->getId(),
                    'masked_number' => $card->getMaskedNumber(),
                    'card_type' => $card->getCardType(),
                    'expiration_date' => $card->getExpirationDate()
                ];
            }
        }
        return $dataReturn;
    }
}
