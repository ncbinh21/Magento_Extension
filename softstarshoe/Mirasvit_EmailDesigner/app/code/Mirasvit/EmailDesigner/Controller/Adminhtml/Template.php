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



namespace Mirasvit\EmailDesigner\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\EmailDesigner\Model\TemplateFactory;

abstract class Template extends Action
{
    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * Constructor
     *
     * @param TemplateFactory $templateFactory
     * @param Registry        $registry
     * @param Context         $context
     */
    public function __construct(
        TemplateFactory $templateFactory,
        Registry $registry,
        Context $context
    ) {
        $this->templateFactory = $templateFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->backendSession = $context->getSession();

        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magento_Backend::marketing');
        $resultPage->getConfig()->getTitle()->prepend(__('Email Designer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Templates'));

        return $resultPage;
    }

    /**
     * Theme model
     *
     * @return \Mirasvit\EmailDesigner\Model\Template
     */
    public function initModel()
    {
        $model = $this->templateFactory->create();

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('current_model', $model);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_EmailDesigner::email_designer_template');
    }
}
