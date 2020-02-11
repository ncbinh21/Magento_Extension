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
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Controller\Adminhtml\Rewrite;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Mirasvit\Seo\Controller\Adminhtml\Rewrite
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $model = $this->_initModel();

        if ($model->getId()) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Rewrite'));
            $this->_initAction();

            return $resultPage;
        } else {
            $this->messageManager->addError(__('The rewrite does not exist.'));
            $this->_redirect('*/*/');
        }
    }
}
