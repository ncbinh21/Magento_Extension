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



///*******************************************
//Mirasvit
//This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
//Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
//If you wish to customize this module for your needs
//Please refer to http://www.magentocommerce.com for more information.
//@category Mirasvit
//@copyright Copyright (C) 2012 Mirasvit (http://mirasvit.com.ua), Vladimir Drok <dva@mirasvit.com.ua>,
// Alexander Drok<alexander@mirasvit.com.ua>
//*******************************************/


namespace Mirasvit\Seo\Controller\Adminhtml\System;

abstract class Template extends \Magento\Backend\App\Action
{
    /**
     * @var \Mirasvit\Seo\Model\System\Template\Worker
     */
    protected $systemTemplateWorker;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $design;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @param \Mirasvit\Seo\Model\System\Template\Worker $systemTemplateWorker
     * @param \Magento\Framework\View\DesignInterface    $design
     * @param \Magento\Backend\App\Action\Context        $context
     */
    public function __construct(
        \Mirasvit\Seo\Model\System\Template\Worker $systemTemplateWorker,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->systemTemplateWorker = $systemTemplateWorker;
        $this->design = $design;
        $this->context = $context;
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Seo::seo');
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this|\Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        // @fixme
        return parent::dispatch($request);
        $this->design->setTheme('mirasvit');

        return $this;
    }
}
