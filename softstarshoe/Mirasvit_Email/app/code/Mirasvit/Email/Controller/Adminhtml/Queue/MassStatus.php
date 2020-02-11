<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Controller\Adminhtml\Queue;

use Mirasvit\Email\Controller\Adminhtml\Queue;

class MassStatus extends Queue
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $status = $this->getRequest()->getParam('status');

        $changed = 0;
        foreach ($this->getRequest()->getParam('queue') as $id) {
            $this->queueFactory->create()->load($id)
                ->setStatus($status)
                ->setStatusMessage('Manually change')
                ->save();
            $changed++;
        }

        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been changed.', $changed)
        );

        return $resultRedirect->setPath('*/*/');
    }
}
