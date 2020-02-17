<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Clear extends Action
{
    /**
     * @var \Amasty\Fpc\Model\ResourceModel\Log
     */
    private $logResource;

    public function __construct(
        Context $context,
        \Amasty\Fpc\Model\ResourceModel\Log $logResource
    ) {
        parent::__construct($context);
        $this->logResource = $logResource;
    }

    public function execute()
    {
        try {
            $this->logResource->flush();

            $this->messageManager->addSuccessMessage(__('Warmer log has been successfully cleared.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect('*/*/');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Fpc::log_clear');
    }
}
