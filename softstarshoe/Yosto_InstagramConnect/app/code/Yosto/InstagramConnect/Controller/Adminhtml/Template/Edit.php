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
 * Class Edit
 * @package Yosto\InstagramConnect\Controller\Adminhtml\Template
 */
class Edit extends Template
{

    /**
     * Execute when editing
     * @return \Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        $templateId = $this->getRequest()->getParam(Constant::INSTAGRAM_TEMPLATE_ID);
        $model = $this->_templateFactory->create();
        if ($templateId) {
            $model->load($templateId);
            if (!$model->getTemplateId()) {
                $this
                    ->messageManager
                    ->addError(__(Constant::TEMPLATE_IS_NOT_EXIST));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_session->getTemplateData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('instagramconnect_template', $model);

        $resulPage = $this->_resultPageFactory->create();
        $resulPage->setActiveMenu('Yosto_InstagramConnect::template_management');
        if ($templateId) {
            $resulPage->getConfig()->getTitle()->prepend(__('Edit Template'));
        } else {
            $resulPage->getConfig()->getTitle()->prepend(__('New Template'));
        }

        return $resulPage;
    }

}