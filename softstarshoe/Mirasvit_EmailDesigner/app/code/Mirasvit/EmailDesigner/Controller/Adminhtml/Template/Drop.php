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



namespace Mirasvit\EmailDesigner\Controller\Adminhtml\Template;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\EmailDesigner\Controller\Adminhtml\Template;
use Mirasvit\EmailDesigner\Model\TemplateFactory;
use Mirasvit\EmailDesigner\Model\Variable\Pool as VariablePool;

class Drop extends Template
{
    /**
     * @var VariablePool
     */
    protected $variablePool;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        VariablePool $variablePool,
        TemplateFactory $templateFactory,
        Registry $registry,
        Context $context
    ) {
        $this->variablePool = $variablePool;

        parent::__construct($templateFactory, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $model = $this->initModel();

        if (strpos($_SERVER['HTTP_HOST'], 'm2.mirasvit.com') === false) {
            foreach ($this->getRequest()->getParams() as $key => $value) {
                $model->setAreaText($key, $value);
            }
        }

        $variables = $this->variablePool->getRandomVariables();
        $variables['preview'] = true;

        $this->getResponse()->setBody($model->getProcessedTemplateText($variables));
    }
}
