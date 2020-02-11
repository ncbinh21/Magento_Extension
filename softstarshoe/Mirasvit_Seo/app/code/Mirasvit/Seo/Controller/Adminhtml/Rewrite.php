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
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Controller\Adminhtml;

abstract class Rewrite extends \Magento\Backend\App\Action
{
    /**
     * @var \Mirasvit\Seo\Model\RewriteFactory
     */
    protected $rewriteFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $design;

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
     * @param \Mirasvit\Seo\Model\RewriteFactory      $rewriteFactory
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Backend\App\Action\Context     $context
     */
    public function __construct(
        \Mirasvit\Seo\Model\RewriteFactory $rewriteFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->rewriteFactory = $rewriteFactory;
        $this->registry = $registry;
        $this->design = $design;
        $this->context = $context;
        $this->backendSession = $context->getSession();
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Seo::seo_rewrite');
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_setActiveMenu('seo');

        return $this;
    }

    /**
     * @return \Mirasvit\Seo\Model\Rewrite
     */
    public function _initModel()
    {
        $model = $this->rewriteFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('current_model', $model);

        return $model;
    }
}
