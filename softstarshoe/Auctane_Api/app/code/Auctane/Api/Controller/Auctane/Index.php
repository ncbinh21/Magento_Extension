<?php

namespace Auctane\Api\Controller\Auctane;

use Exception;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Default function
     *
     * @return void
     */
    public function execute()
    {
        $authUser = $this->getRequest()->getParam('SS-UserName');
        $authPassword = $this->getRequest()->getParam('SS-Password');

        // \Magento\Store\Model\StoreManagerInterface $storeManager
        $storeManager = $this->_objectManager->get(
            'Magento\Store\Model\StoreManagerInterface'
        );
        $storeId = $storeManager->getStore()->getId();
        
        $storageInterface = $this->_objectManager->get(
            '\Magento\Backend\Model\Auth\Credential\StorageInterface'
        );
        $userAuthentication = $storageInterface->authenticate(
            $authUser,
            $authPassword
        );

        $dataHelper = $this->_objectManager->get('Auctane\Api\Helper\Data');
        if (!$userAuthentication) {
            header(sprintf('WWW-Authenticate: Basic realm=ShipStation'));
            $result = $dataHelper->fault(401, 'Authentication failed');
            header('Content-Type: text/xml; charset=UTF-8');
            $this->getResponse()->setBody($result);
            return false;
        }

        //Get the requested action
        $action = $this->getRequest()->getParam('action');
        try {
            switch ($action) {
                case 'export':
                    $export = $this->_objectManager->get(
                        'Auctane\Api\Model\Action\Export'
                    );
                    $result = $export->process($this->getRequest(), $storeId);
                    break; 

                case 'shipnotify':
                    $shipNotify = $this->_objectManager->get(
                        'Auctane\Api\Model\Action\ShipNotify'
                    );
                    $result = $shipNotify->process($this->getRequest());
                    // if there hasn't been an error then "200 OK" is given
                    break;
            }

        } catch (Exception $fault) {
            $result = $dataHelper->fault($fault->getCode(), $fault->getMessage());
        }
        
        $this->getResponse()->setBody($result);
    }
}