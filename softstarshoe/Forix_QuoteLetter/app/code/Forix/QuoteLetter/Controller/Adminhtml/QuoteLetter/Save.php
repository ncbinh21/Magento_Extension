<?php
/**
 * Hidro Forix Webdesign. 
 * Copyright (C) 2017  Hidro Le
 * 
 * This file included in Forix/QuoteLetter is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Forix\QuoteLetter\Controller\Adminhtml\QuoteLetter;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    protected $_quoteLetterFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Forix\QuoteLetter\Model\QuoteLetterFactory $quoteLetterFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_quoteLetterFactory = $quoteLetterFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('quoteletter_id');
        
            $model = $this->_quoteLetterFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Quoteletter no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            if(isset($data['product_skus']) && $data['product_skus']){
                if(is_string($data['product_skus'])) {
                    if (null !== ($encoded = json_decode($data['product_skus']))) {
                        $data['product_skus'] = [];
                        foreach ($encoded as $key => $value) {
                            $data['product_skus'][] = $value;
                        }
                    }
                }
            }
            $model->setData($data);
            try {
                $model->getResource()->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the Quoteletter.'));
                $this->dataPersistor->clear('forix_quoteletter_quoteletter');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['quoteletter_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Quoteletter.'));
            }
        
            $this->dataPersistor->set('forix_quoteletter_quoteletter', $data);
            return $resultRedirect->setPath('*/*/edit', ['quoteletter_id' => $this->getRequest()->getParam('quoteletter_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
