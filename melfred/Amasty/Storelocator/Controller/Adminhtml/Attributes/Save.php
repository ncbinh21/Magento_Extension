<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Storelocator
 */


namespace Amasty\Storelocator\Controller\Adminhtml\Attributes;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Amasty\Storelocator\Controller\Adminhtml\Attributes
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $data = $this->getRequest()->getPostValue();

            try {
                /** @var \Amasty\Storelocator\Model\Attribute $model */
                $model = $this->attributeFactory->create();

                $id = $this->getRequest()->getParam('attribute_id');

                if ($id) {
                    $this->attributeResourceModel->load($model, $id);
                    if ($id != $model->getId()) {
                        throw new LocalizedException(__('The wrong data is specified.'));
                    }
                }

                $frontendLabels = [];
                if (is_array($data)) {
                    $frontendLabels = $data['frontend_label'];
                    $defaultLabel = null;
                    if (isset($frontendLabels[0])) {
                        $defaultLabel = $frontendLabels[0];
                        unset($frontendLabels[0]);
                    }
                    $data['frontend_label'] = $defaultLabel;
                }
                $data['label_serialized'] = $this->serializer->serialize($frontendLabels);

                $model->setData($data);

                $this->attributeResourceModel->save($model);

                $this->attributeResourceModel->saveOptions($data, $model->getId());

                $this->messageManager->addSuccessMessage(__('Record has been successfully saved'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('*/*/');
                return;

            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $id = (int)$this->getRequest()->getParam('attribute_id');
                if (!empty($id)) {
                    $this->_redirect('*/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('*/*/index');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving data. Please review the error log.')
                );
                $this->logInterface->critical($e);
                $this->session->setPageData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('attribute_id')]);
                return;
            }
        }
    }

}