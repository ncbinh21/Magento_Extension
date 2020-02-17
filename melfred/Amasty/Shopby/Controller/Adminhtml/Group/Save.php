<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Controller\Adminhtml\Group;

use Amasty\ShopbyBase\Model\Cache\Type;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends \Amasty\Shopby\Controller\Adminhtml\Group
{

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('group_id');
            $code = $this->getRequest()->getParam('group_code');
            if ($id) {
                try {
                    $model = $this->groupAttrRepository->get($id);
                } catch (NoSuchEntityException $e) {
                    $this->messageManager->addError(__('This group no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            } else {
                $model = $this->groupAttrFactory->create();
            }
            if (!$id || (($model->getId() && $id) && $model->getGroupCode() != $code)) {
                if ($this->groupAttrFactory->create()->getCollection()
                    ->addFieldToFilter(\Amasty\Shopby\Model\GroupAttr::GROUP_CODE, $code)->getSize()
                ) {
                    $this->messageManager->addError(__('This group code already exists.'));
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['group_id' => $this->getRequest()->getParam('group_id')]
                    );
                }
            }
            $model->setData($data);
            try {
                $this->groupAttrRepository->save($model);
                $this->cacheTypeList->invalidate(Type::TYPE_IDENTIFIER);
                $this->messageManager->addSuccess(__('You saved the group.'));
                $this->sessionFactory->create()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['group_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->sessionFactory->create()->setFormData($data);
                return $resultRedirect->setPath('*/*/edit', ['group_id' => $this->getRequest()->getParam('group_id')]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Shopby::group_attributes');
    }
}
