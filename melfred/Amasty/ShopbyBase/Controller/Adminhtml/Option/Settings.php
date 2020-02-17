<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Controller\Adminhtml\Option;

class Settings extends \Amasty\ShopbyBase\Controller\Adminhtml\Option
{
    public function execute()
    {
        $resultLayout = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_LAYOUT);
        return $resultLayout;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_ShopbyBase::option');
    }
}
