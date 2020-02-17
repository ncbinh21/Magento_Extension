<?php
/**
 * Created by PhpStorm.
 * User: magenest
 * Date: 12/03/2017
 * Time: 15:13
 */

namespace Magenest\SagepayUS\Controller\Checkout;

use Magenest\SagepayUS\Controller\Checkout;
use Magento\Framework\App\ResponseInterface;

class Request extends Checkout
{

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            if (!$this->_formKeyValidator->validate($this->getRequest())) {
                return $this->jsonFactory->create()->setData([
                    'error' => true,
                    'message' => 'Payment Error'
                ]);
            }
            $quote = $this->checkoutSession->getQuote();
            $isToken = $this->getRequest()->getParam('is_token');
            if ($quote->getIsActive() || ($isToken && $this->configHelper->getCanSaveCard())) {
                $cardId = $this->getRequest()->getParam('card_id');
                $vault = $this->vaultFactory->create()->load($cardId);
                /** @var \Magento\Customer\Model\Session $customer */
                $customer = $this->_objectManager->create('Magento\Customer\Model\Session');
                $customerId = $customer->getCustomerId();
                $cardToken = '';
                if($vault->isOwnCard($customerId)){
                    $cardToken = $vault->getCardId();
                }
                $payAction = $this->configHelper->getPaymentAction();
                $nonces = $this->configHelper->getNonces();
                $merchant = [
                    "ID" => $this->configHelper->getMerchantId(),
                    "KEY" => $this->configHelper->getMerchantKey()
                ];
                // sign up at https://developer.sagepayments.com/ to get your own dev creds
                $developer = [
                    "ID" => $this->configHelper->getDeveloperId(),
                    "KEY" => $this->configHelper->getDeveloperKey()
                ];
                $req = [
                    "merchantId" => $merchant['ID'],
                    "merchantKey" => $merchant['KEY'],
                    "requestType" => "payment",
                    "orderNumber" => time(),
                    "amount" => $quote->getBaseGrandTotal(),// use 5.00 to simulate a decline
                    "salt" => $nonces['salt'],
                    "postbackUrl" => $this->_storeManager->getStore()->getBaseurl() . 'sagepayus/checkout/response',
                    "preAuth" => (bool)($payAction=='authorize'),
                    "doVault" => (bool)$this->configHelper->getCanSaveCard(),
                    "data" => $quote->getBaseGrandTotal(),
                ];
                if($cardToken){
                    $req['token'] = $cardToken;
                }
                if($isToken){
                    unset($req['amount']);
                    unset($req['preAuth']);
                    unset($req['token']);
                    unset($req['data']);
                    $req['doVault'] = true;
                    $req['requestType'] = "vault";
                }

                $authKey = $this->configHelper->getAuthKey(
                    json_encode($req),
                    $developer['KEY'],
                    $nonces['salt'],
                    $nonces['iv']
                );
                unset($req['merchantKey']);
                $dataReturn = [
                    'error' => false,
                    'success' => true,
                    'authKey' => $authKey,
                    'clientId' => $developer['ID'],
                    'environment' => $this->configHelper->getEnvironment(),
                    'debug' => (bool)(int)$this->configHelper->getSageBrowserDebug()
                ];

                return $this->jsonFactory->create()->setData(array_merge($dataReturn, $req));
            } else {
                return $this->jsonFactory->create()->setData([
                    'error' => true,
                    'message' => 'Operation is not allow'
                ]);
            }
        }

        return $this->jsonFactory->create()->setData([
            'error' => true,
            'message' => 'Payment Error'
        ]);
    }
}
