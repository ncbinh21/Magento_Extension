<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Controller\Adminhtml\Pool;

use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Giftcard\Api\PoolRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

/**
 * Class Edit
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Pool
 */
class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Giftcard::giftcard_pools';

    /**
     * @var PoolRepositoryInterface
     */
    private $poolRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PoolRepositoryInterface $poolRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PoolRepositoryInterface $poolRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->poolRepository = $poolRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $poolId = (int)$this->getRequest()->getParam('id');
        if ($poolId) {
            try {
                $this->poolRepository->get($poolId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This pool no longer exists')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_Giftcard::giftcard_pools')
            ->getConfig()->getTitle()->prepend(
                $poolId ? __('Edit Code Pool') : __('New Code Pool')
            );
        return $resultPage;
    }
}
