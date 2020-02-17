<?php

namespace Magenest\SagepayUS\Controller\Card;

use Magenest\SagepayUS\Controller\Card;
use Magento\Framework\Controller\ResultFactory;

class Save extends Card
{
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $sageResp = $this->getRequest()->getParam('sage_resp');
        $sageHash = $this->getRequest()->getParam('sage_hash');
        $sageCardInfo = $this->getRequest()->getParam('sage_cardinfo');
        if($this->_customerSession->isLoggedIn()){
            if($this->configHelper->validateResponse($sageResp, $sageHash)){
                $payRespObject = json_decode($sageResp, true);
                $responseObject = @$payRespObject['gatewayResponse'];
                if($this->configHelper->saveCard(@$responseObject['vaultResponse'], $sageCardInfo)) {
                    return $result->setData([
                        'error' => false,
                        'success' => true
                    ]);
                }else{
                    return $result->setData([
                        'error' => true,
                        'success' => false,
                        'message' => __("Operation is not allow")
                    ]);
                }
            }else{
                return $result->setData([
                    'error' => true,
                    'success'=> false,
                    'message' => __("Invalid response")
                ]);
            }
        }else{
            return $result->setData([
                'error' => true,
                'success'=> false,
                'message' => __("Customer is not logged in")
            ]);
        }
    }
}
