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

use Mirasvit\Seo\Model\Config as Config;

class Save extends \Mirasvit\Seo\Controller\Adminhtml\Template
{
    /**
     * @return void
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getParams()) {
            $data['sort_order'] = (isset($data['sort_order']) &&
                trim($data['sort_order']) != '') ? (int) trim($data['sort_order']) : 10;
            $data = $this->prepareStoreIds($data);
            $data = $this->prepareCompatibility($data);
           
            $model = $this->_initModel();
            $model->setData($data);


            try {
                $model->save();
                $this->messageManager->addSuccess(__('Item was successfully saved'));
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $type = $model->getRuleType();
                    switch ($type) {
                        case Config::PRODUCTS_RULE:
                            $path = '*/*/editProduct';
                            break;

                        case Config::CATEGORIES_RULE:
                            $path = '*/*/editCategory';
                            break;

                        case Config::RESULTS_LAYERED_NAVIGATION_RULE:
                            $path = '*/*/editLayeredNavigation';
                            break;

                        default:
                            break;
                    }
                    $this->_redirect($path, ['id' => $model->getId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->backendSession->setFormData($data);
                $this->_redirect('*/*/');

                return;
            }
        }
        $this->messageManager->addError(__('Unable to find item to save'));
        $this->_redirect('*/*/');
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
