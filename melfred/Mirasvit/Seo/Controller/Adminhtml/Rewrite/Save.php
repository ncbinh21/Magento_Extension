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



namespace Mirasvit\Seo\Controller\Adminhtml\Rewrite;

class Save extends \Mirasvit\Seo\Controller\Adminhtml\Rewrite
{
    /**
     * @return void
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getParams()) {
            $data = $this->prepareStoreIds($data);
            $_model = $this->_initModel();
            $_model->setData($data);

            try {
                $_model->save();

                $this->messageManager->addSuccess(__('Rewrite was successfully saved'));
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $_model->getId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->backendSession->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

                return;
            }
        }
        $this->messageManager->addError(__('Unable to find rewrite to save'));
        $this->_redirect('*/*/');
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareStoreIds($data)
    {
        if (isset($data['use_config']['stores'])
            && $data['use_config']['stores'] == 'true') {
            $data['stores'] = [0];
        } elseif (isset($data['store_id'])) {
            $data['stores'] = $data['store_id'];
        }

        return $data;
    }
}
