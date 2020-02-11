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



namespace Mirasvit\Seo\Controller\Adminhtml\Template;

class Delete extends \Mirasvit\Seo\Controller\Adminhtml\Template
{
    /**
     * @return void
     */
    public function execute()
    {
        if (($ruleId = $this->getRequest()->getParam('id')) && $ruleId > 0) {
            try {
                $model = $this->templateFactory->create();
                $model->setId($ruleId)->delete();

                $this->messageManager->addSuccess(
                    __('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()
                    ->getParam('id'), ]);
            }
        }
        $this->_redirect('*/*/');
    }
}
