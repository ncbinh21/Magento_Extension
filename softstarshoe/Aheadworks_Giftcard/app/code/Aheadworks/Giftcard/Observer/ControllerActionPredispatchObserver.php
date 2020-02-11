<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Backend\Model\Session;
use Magento\Framework\App\ActionFlag;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Action\Action;

/**
 * Class ControllerActionPredispatchObserver
 *
 * @package Aheadworks\Giftcard\Observer
 */
class ControllerActionPredispatchObserver implements ObserverInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param Session $session
     * @param ActionFlag $actionFlag
     * @param UrlInterface $url
     */
    public function __construct(
        Session $session,
        ActionFlag $actionFlag,
        UrlInterface $url
    ) {
        $this->session = $session;
        $this->actionFlag = $actionFlag;
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\App\Action\Action $controller */
        $controller = $observer->getEvent()->getControllerAction();
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $observer->getEvent()->getRequest();
        $actionName = $request->getFullActionName();

        switch ($actionName) {
            case 'catalog_product_index':
                $this->session->setResetBackToAwGiftcardGridFlag(true);
                if ($controller->getRequest()->getParam('menu', false)) {
                    $this->session->setBackToAwGiftcardGridFlag(false);
                } else {
                    if ($this->session->getBackToAwGiftcardGridFlag()) {
                        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
                        $this->actionFlag->set('', Action::FLAG_NO_POST_DISPATCH, true);

                        $giftcardParams = $this->session->getBackToAwGiftcardParams();
                        $this->session->getBackToAwGiftcardParams(null);
                        if (is_array($giftcardParams) && isset($giftcardParams['awgcBack'])) {
                            $backUrl = $giftcardParams['awgcBack'];
                        } else {
                            $backUrl = $this->session->getBackToAwGiftcardParams();
                        }

                        $params = [];
                        if ($backUrl == 'code') {
                            $url = 'aw_giftcard_admin/giftcard/index';
                        } elseif ($backUrl == 'editCode' && isset($giftcardParams['awgcId'])) {
                            $url = 'aw_giftcard_admin/giftcard/edit';
                            $params = ['id' => $giftcardParams['awgcId']];
                        } else {
                            $url = 'aw_giftcard_admin/product/index';
                        }

                        $controller->getResponse()->setRedirect($this->url->getUrl($url, $params));
                    }
                }
                break;
            case 'catalog_product_edit':
            case 'catalog_product_new':
                if ($this->session->getResetBackToAwGiftcardGridFlag()) {
                    $this->session->setBackToAwGiftcardGridFlag(false);
                }
                $awGcParams = [];
                foreach ($request->getParams() as $key => $value) {
                    $result = strpos($key, 'awgc');
                    if ($result === 0) {
                        $awGcParams[$key] = $value;
                    }
                }
                $this->session->setBackToAwGiftcardParams($awGcParams);
                break;
        }
        return $this;
    }
}
