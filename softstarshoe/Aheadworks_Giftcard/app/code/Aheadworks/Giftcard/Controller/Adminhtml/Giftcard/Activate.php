<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Controller\Adminhtml\Giftcard;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Aheadworks\Giftcard\Model\Source\Giftcard\Status;

/**
 * Class Activate
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Giftcard
 */
class Activate extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Giftcard::giftcard_codes';

    /**
     * @var GiftcardRepositoryInterface
     */
    private $giftcardRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        GiftcardRepositoryInterface $giftcardRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->giftcardRepository = $giftcardRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Activate action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($giftcardId = (int)$this->getRequest()->getParam('id')) {
            try {
                $giftcardCode = $this->giftcardRepository->get($giftcardId);
                $giftcardCode->setState(Status::ACTIVE);
                $this->giftcardRepository->save($giftcardCode);
                $this->messageManager->addSuccessMessage(__('Gift Card code was successfully activated'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while activating Gift Card code')
                );
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
