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



namespace Mirasvit\Seo\Controller\Adminhtml\CanonicalRewrite;

use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;
use Mirasvit\Seo\Controller\Adminhtml\CanonicalRewrite;

class Save extends CanonicalRewrite
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam(CanonicalRewriteInterface::ID_ALIAS);

        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $data['sort_order'] = (isset($data['sort_order']) &&
                trim($data['sort_order']) != '') ? (int) trim($data['sort_order']) : 10;
            $data = $this->prepareStoreIds($data);
            $data = $this->prepareCompatibility($data);
            
            $model = $this->initModel();
            if ($id && !$model) {
                $this->messageManager->addErrorMessage(__('This row no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            foreach ($data as $key => $value) {
                $model->setDataUsingMethod($key, $value);
            }

            try {
                $this->canonicalRewriteRepository->save($model);

                $this->messageManager->addSuccessMessage(__('Row was saved.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [CanonicalRewriteInterface::ID_ALIAS => $model->getId()]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', [CanonicalRewriteInterface::ID_ALIAS => $id]);
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareStoreIds($data)
    {
        if (isset($data['use_config']['store_ids'])
            && $data['use_config']['store_ids'] == 'true') {
            $data['store_ids'] = [0];
        } elseif (isset($data['store_id'])) {
            $data['store_ids'] = $data['store_id'];
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareCompatibility($data)
    {
        if (isset($data['conditions_serialized']) 
            && $data['conditions_serialized']) {
                $data['conditions_serialized'] = $this->compatibilityService
                    ->prepareRuleDataForSave($data['conditions_serialized']);
        }
        if (isset($data['actions_serialized'])
            && $data['actions_serialized']) {
                $data['actions_serialized'] = $this->compatibilityService
                    ->prepareRuleDataForSave($data['actions_serialized']);
        }

        return $data;
    }
}
