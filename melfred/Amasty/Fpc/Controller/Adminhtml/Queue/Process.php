<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Controller\Adminhtml\Queue;

use Amasty\Fpc\Model\Queue;
use Magento\Backend\App\Action\Context;

class Process extends \Amasty\Fpc\Controller\Adminhtml\Queue
{
    /**
     * @var Queue
     */
    private $queue;

    public function __construct(
        Context $context,
        Queue $queue
    ) {
        parent::__construct($context);
        $this->queue = $queue;
    }

    public function execute()
    {
        try {
            $this->queue->process();

            $this->messageManager->addSuccessMessage(
                __('First batch of warmer queue has been successfully processed.')
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect('*/*/');
    }
}
