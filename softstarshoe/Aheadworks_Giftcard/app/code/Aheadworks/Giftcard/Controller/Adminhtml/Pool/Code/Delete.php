<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Controller\Adminhtml\Pool\Code;

use Aheadworks\Giftcard\Api\PoolCodeRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

/**
 * Class Delete
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Pool\Code
 */
class Delete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Giftcard::giftcard_pools';

    /**
     * @var PoolCodeRepositoryInterface
     */
    private $poolCodeRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PoolCodeRepositoryInterface $poolCodeRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PoolCodeRepositoryInterface $poolCodeRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->poolCodeRepository = $poolCodeRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($codeId = (int)$this->getRequest()->getParam('id')) {
            try {
                $this->poolCodeRepository->deleteById($codeId);
                $this->messageManager->addSuccessMessage(__('Code was successfully deleted'));
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
