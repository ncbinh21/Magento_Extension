<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */

namespace Amasty\OrderStatus\Controller\Adminhtml\Status;

use Magento\Backend\App\Action;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Amasty\OrderStatus\Model\ResourceModel\Template
     */
    private $template;

    /**
     * @var \Amasty\OrderStatus\Model\Status
     */
    private $statusFactory;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param \Amasty\OrderStatus\Model\ResourceModel\Template $template
     * @param \Amasty\OrderStatus\Model\StatusFactory $statusFactory
     */
    public function __construct(
        Action\Context $context,
        \Amasty\OrderStatus\Model\ResourceModel\Template $template,
        \Amasty\OrderStatus\Model\StatusFactory $statusFactory
    ) {
        $this->template = $template;
        $this->statusFactory = $statusFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $this->template->removeStatusTemplates($id);
                /** @var \Amasty\OrderStatus\Model\Status $status */
                $status = $this->statusFactory->create();
                $status->setId($id);
                $status->getResource()->delete($status);
                $this->messageManager->addSuccessMessage(__('The status has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('Unable to find status to delete.'));
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_OrderStatus::amostatus');
    }
}
