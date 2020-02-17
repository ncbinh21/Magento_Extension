<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: melfredborzall.local
 */

namespace Forix\Custom\Observer;


class DisableSearchResult implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getRequest();
        $request->initForward();
        $request->setActionName('noroute');
        $request->setDispatched(false);
    }
}