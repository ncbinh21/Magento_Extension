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
 * @package   mirasvit/module-report
 * @version   1.2.27
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Report\Controller\Adminhtml\Email;

use Mirasvit\Report\Controller\Adminhtml\Email;

class Send extends Email
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $model = $this->initModel();

        $this->emailService->send($model);

        $this->messageManager->addSuccess(__('Email was sent.'));

        return $this->resultRedirectFactory->create()
            ->setPath('*/*/edit', ['id' => $model->getId()]);
    }
}
