<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: Soft Star Shoes
 * Date: 31 Jan 2018
 * Time: 11:08 PM
 */

namespace Forix\QuoteLetter\Controller\Adminhtml\QuoteLetter;
use Magento\Backend\App\Action;
class MassDisabled  extends \Magento\Backend\App\Action
{
    protected $_quoteLetterFactory;
    public function __construct(
        Action\Context $context,
        \Forix\QuoteLetter\Model\QuoteLetterFactory $quoteLetterFactory
    )
    {
        parent::__construct($context);
        $this->_quoteLetterFactory = $quoteLetterFactory;
    }
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $quoteletterIds = $this->getRequest()->getParam('quoteletter');
        if (!is_array($quoteletterIds) || empty($quoteletterIds)) {
            $this->messageManager->addError(__('Please select quote letter(s).'));
        } else {
            try {
                $sliderCollection = $this->_quoteLetterFactory->create()->getCollection()
                    ->addFieldToFilter('quoteletter_id', ['in' => $quoteletterIds]);

                foreach ($sliderCollection as $slider) {
                    $slider->setIsActive(0)
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been changed status.', count($quoteletterIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
