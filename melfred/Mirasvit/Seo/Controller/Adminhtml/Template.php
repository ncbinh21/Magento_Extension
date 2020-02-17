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
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Controller\Adminhtml;

abstract class Template extends \Magento\Backend\App\Action
{
    /**
     * @var \Mirasvit\Seo\Model\TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * Template constructor.
     * @param \Mirasvit\Seo\Api\Service\CompatibilityServiceInterface $compatibilityService
     * @param \Mirasvit\Seo\Model\TemplateFactory $templateFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mirasvit\Seo\Api\Service\MessageInterface $message
     */
    public function __construct(
        \Mirasvit\Seo\Api\Service\CompatibilityServiceInterface $compatibilityService,
        \Mirasvit\Seo\Model\TemplateFactory $templateFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context,
        \Mirasvit\Seo\Api\Service\MessageInterface $message
    ) {
        $this->compatibilityService = $compatibilityService;
        $this->templateFactory = $templateFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->message = $message;
        $this->backendSession = $context->getSession();
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Seo::seo_template');
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_setActiveMenu('Mirasvit_Seo::seo');

        return $this;
    }

    /**
     * @return \Mirasvit\Seo\Model\Template
     */
    protected function _initModel()
    {
        $model = $this->templateFactory->create();

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('current_template_model', $model);

        return $model;
    }
}
