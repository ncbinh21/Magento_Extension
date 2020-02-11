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
 * Class Save
 * @package Yosto\InstagramConnect\Controller\Adminhtml\Template
 */
class Save extends Template
{

    /**
     * Execute when saving
     */
    public function execute()
    {
        $isPost = $this->getRequest()->getPost();
        if ($isPost) {
            $templateModel = $this->_templateFactory->create();
            $templateId = $this->getRequest()->getParam(Constant::INSTAGRAM_TEMPLATE_ID);
            if ($templateId) {
                $templateModel->load($templateId);
            }
            $formData = $this->getRequest()->getParam('template');
            $templateModel->setData($formData);

            $validateResult = $this->validateTemplate($templateModel);
            if ($validateResult[Constant::VALIDATE_STATUS] == false) {
                $this->_session->setTemplateData($formData);
                $this
                    ->messageManager
                    ->addError(__($validateResult[Constant::VALIDATE_MESSAGE]));
                $this->_redirect(
                    '*/*/edit', $validateResult[Constant::VALIDATE_PARAMS]
                );
                return;
            }
            try {
                $templateModel->save();

                $this
                    ->messageManager
                    ->addSuccess(__(Constant::TEMPLATE_SAVE_SUCCESS));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit',
                        [
                            Constant::INSTAGRAM_TEMPLATE_ID => $templateModel
                                ->getTemplateId(),
                            '_current' => true
                        ]
                    );
                    return;
                }

                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($formData);
            $this->_redirect(
                '*/*/index',
                [
                    Constant::INSTAGRAM_TEMPLATE_ID => $templateId
                ]
            );
        }
    }

    /**
     * @param $templateModel
     * @return array
     */
    public function validateTemplate($templateModel)
    {
        if (!$templateModel->getTemplateId()) {
            $templateWithSameName = $templateModel
                ->getCollection()
                ->addFieldToFilter(
                    Constant::INSTAGRAM_TEMPLATE_TABLE_TITLE,
                    $templateModel->getTitle()
                );
            $params = ['_current' => true];
        } else {
            $templateWithSameName = $templateModel
                ->getCollection()
                ->addFieldToFilter(
                    Constant::INSTAGRAM_TEMPLATE_TABLE_TITLE,
                    $templateModel->getTitle()
                )
                ->addFieldToFilter(
                    Constant::INSTAGRAM_TEMPLATE_ID,
                    [
                        Constant::QUERY_REQUIRE_NOT_EQUAL => $templateModel
                            ->getTemplateId()
                    ]
                );
            $params = [
                Constant::INSTAGRAM_TEMPLATE_ID => $templateModel->getTemplateId(),
                '_current' => true
            ];
        }

        if (count($templateWithSameName) > 0) {
            $templateId = $templateWithSameName->getFirstItem()->getTemplateId();
            return [
                Constant::VALIDATE_STATUS => false,
                Constant::VALIDATE_MESSAGE => __(
                    Constant::TEMPLATE_SAVE_DUPLICATE_TITLE . $templateId . ' !'
                ),
                Constant::VALIDATE_PARAMS => $params
            ];
        } else {
            return [
                Constant::VALIDATE_STATUS => true,
                Constant::VALIDATE_MESSAGE => __(Constant::VALIDATE_SUCCESS)
            ];
        }

    }
}