<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Controller\Adminhtml\Template;

use Yosto\InstagramConnect\Controller\Adminhtml\Template;
use Yosto\InstagramConnect\Helper\Constant;

/**
 * Class Delete
 * @package Yosto\InstagramConnect\Controller\Adminhtml\Template
 */
class Delete extends Template
{

    /**
     * Execute when deleting
     */
    public function execute()
    {
        $templateId = $this->getRequest()->getParam(Constant::INSTAGRAM_TEMPLATE_ID);
        if ($templateId) {
            $templateModel = $this->_templateFactory->create();
            $templateModel->load($templateId);
            if (!$templateModel->getTemplateId()) {
                $this
                    ->messageManager
                    ->addError(__(Constant::TEMPLATE_IS_NOT_EXIST));
            } else {
                try {
                    $templateModel->delete();
                    $this
                        ->messageManager
                        ->addSuccess(__(Constant::TEMPLATE_DELETE_SUCCESS));
                    $this->_redirect('*/*/');
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect(
                        '*/*/edit',
                        [Constant::COMMON_ID => $templateModel->getTemplateId()]
                    );
                }
            }
        }
    }
}