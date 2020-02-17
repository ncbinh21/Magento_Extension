<?php

namespace Magenest\SagepayUS\Controller\Card;

use Magenest\SagepayUS\Controller\Card;

class Index extends Card
{
    public function execute()
    {
        $this->_view->loadLayout();
        $block = $this->_view->getLayout()->getBlock('sagepay_customer_card_list');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->getPage()->getConfig()->getTitle()->set(__('My Card Identifiers'));
        $this->_view->renderLayout();
    }
}
