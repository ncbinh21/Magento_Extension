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
use Mirasvit\EmailDesigner\Model\ThemeFactory;

abstract class Theme extends Action
{
    /**
     * @var ThemeFactory
     */
    protected $themeFactory;

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
     * @param ThemeFactory $templateFactory
     * @param Registry     $registry
     * @param Context      $context
     */
    public function __construct(
        ThemeFactory $templateFactory,
        Registry $registry,
        Context $context
    ) {
        $this->themeFactory = $templateFactory;
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
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Themes'));

        return $resultPage;
    }

    /**
     * Theme model
     *
     * @return \Mirasvit\EmailDesigner\Model\Theme
     */
    public function initModel()
    {
        $model = $this->themeFactory->create();

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
        return $this->context->getAuthorization()->isAllowed('Mirasvit_EmailDesigner::email_designer_theme');
    }
}
