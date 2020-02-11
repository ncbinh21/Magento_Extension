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



namespace Mirasvit\Email\Controller\Action;

use Mirasvit\Email\Controller\Action;

class UnsubscribeAll extends Action
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($hash = $this->getRequest()->getParam('hash')) {
            $queue = $this->frontendHelper->getQueue($hash);

            if (!$queue) {
                $this->messageManager->addError(__('Wrong unsubscription link'));
                $this->getResponse()->setRedirect($this->_getUrl('/', true));

                return;
            }

            $this->messageManager
                ->addSuccess(__('You have been successfully unsubscribed from receiving these emails.'));
        }

        $this->getResponse()->setRedirect($this->_getUrl('/', true));
    }
}
