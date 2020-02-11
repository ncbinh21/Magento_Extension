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
 * @package   mirasvit/module-email-designer
 * @version   1.0.16
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailDesigner\Controller\Adminhtml\Theme;

use Mirasvit\EmailDesigner\Controller\Adminhtml\Theme;

class Drop extends Theme
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $model = $this->initModel();

        if (strpos($_SERVER['HTTP_HOST'], 'm2.mirasvit.com') === false) {
            if ($this->getRequest()->getParam('template_text')) {
                $model->setTemplateText($this->getRequest()->getParam('template_text'));
            }
        }

        $this->getResponse()->setBody($model->getProcessedTemplateText(['preview' => true]));
    }

    /**
     * {@inheritdoc}
     */
    public function _processUrlKeys()
    {
        return true;
    }
}
