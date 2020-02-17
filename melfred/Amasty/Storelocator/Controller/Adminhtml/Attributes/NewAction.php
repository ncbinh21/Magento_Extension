<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Storelocator
 */


namespace Amasty\Storelocator\Controller\Adminhtml\Attributes;

class NewAction extends \Amasty\Storelocator\Controller\Adminhtml\Attributes
{

    public function execute()
    {
        $resultForward = $this->forwardFactory->create();
        $resultForward->forward('edit');
        return $resultForward;
    }
}
