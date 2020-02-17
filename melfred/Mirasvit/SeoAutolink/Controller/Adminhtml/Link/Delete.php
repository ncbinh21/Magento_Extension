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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoAutolink\Controller\Adminhtml\Link;

use Magento\Framework\Controller\ResultFactory;

class Delete extends \Mirasvit\SeoAutolink\Controller\Adminhtml\Link
{
    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = $this->linkFactory->create();

                $model->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                $this->messageManager->addSuccess(
                    __('Item was successfully deleted')
                );
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()
                    ->getParam('id'), ]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
