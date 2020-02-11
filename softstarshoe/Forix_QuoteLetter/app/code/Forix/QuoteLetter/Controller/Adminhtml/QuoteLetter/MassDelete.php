<?php

/**
 * Forix
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Forix.com license that is
 * available through the world-wide-web at this URL:
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Forix
 * @package     Forix_Bannerslider
 * @copyright   Copyright (c) 2012 Forix (http://www.forixwebdesign.com/)
 * @license
 */

namespace Forix\QuoteLetter\Controller\Adminhtml\QuoteLetter;
use Magento\Backend\App\Action;

/**
 * Mass Delete Slider action
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class MassDelete  extends \Magento\Backend\App\Action
{
    protected $_quoteLetterFactory;
    public function __construct(
        Action\Context $context,
        \Forix\QuoteLetter\Model\QuoteLetterFactory $quoteLetterFactory
    )
    {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $quoteletterIds = $this->getRequest()->getParam('quoteletter');
        if (!is_array($quoteletterIds) || empty($quoteletterIds)) {
            $this->messageManager->addError(__('Please select Quote Letter(s).'));
        } else {
            $model = $this->_quoteLetterFactory->create();
            $sliderCollection = $model->getCollection()
                ->addFieldToFilter('quoteletter_id', ['in' => $quoteletterIds]);
            try {
                foreach ($sliderCollection as $slider) {
                    $slider->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($quoteletterIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
