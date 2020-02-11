<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */

namespace Amasty\Paction\Controller\Adminhtml\Massaction;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Amasty\Paction\Controller\Adminhtml\Massaction
{
    /*
     * Validate data before using it
     *
     * @param string $amastyPactionField
     * @param string $action
     * @throws \Amasty\Paction\Model\CustomException
     */
    protected function _validateData($amastyPactionField, $action)
    {
        $amastyPactionField = trim($amastyPactionField);

        if (strpos($action, 'amasty_') === 0) {
            $action = str_replace("amasty_", "", $action);
        } else {
            throw new \Amasty\Paction\Model\CustomException(
                __('Something was wrong. Please try again.')
            );
        }

        return [$amastyPactionField, $action];
    }

    /**
     * Update product(s) data action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $idsFromRequest = $this->getRequest()->getParam('selected', 0);
        if (!$idsFromRequest) {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $productIds = $collection->getAllIds();
        } else {
            foreach ($idsFromRequest as $id) {
                $productIds[] = (int)$id;
            }
        }

        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $amastyPactionField = $this->getRequest()->getParam('amasty_paction_field');
        if (!$amastyPactionField) {
            $amastyPactionField = $this->getRequest()->getParam('amasty_file_field');
        }
        $action = $this->getRequest()->getParam('action');

        try {
            list($amastyPactionField, $action) = $this->_validateData($amastyPactionField, $action);
            $className = 'Amasty\Paction\Model\Command\\'  . ucfirst($action);
            if (class_exists($className)) {
                $command = $this->_objectManager->create($className);
                $success = $command->execute($productIds, $storeId, $amastyPactionField);
                if ($success) {
                    $this->messageManager->addSuccess($success);
                }

                // show non critical erroes to the user
                foreach ($command->getErrors() as $err) {
                    $this->messageManager->addError($err);
                }

                $this->_productPriceIndexerProcessor->reindexList($productIds);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Amasty\Paction\Model\CustomException $e) {
            $this->messageManager->addException($e, $e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while updating the product(s) data.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('catalog/product/', ['store' => $storeId]);
    }
}

