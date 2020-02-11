<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Controller\Adminhtml\Earning;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Mirasvit\Credit\Controller\Adminhtml\Earning
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('New Earning Rule'));

        $this->initModel();

        $this->_addContent($resultPage->getLayout()->createBlock('Mirasvit\Credit\Block\Adminhtml\Earning\Edit'))
            ->_addLeft($resultPage->getLayout()->createBlock('Mirasvit\Credit\Block\Adminhtml\Earning\Edit\Tabs'));
        return $resultPage;
    }
}
