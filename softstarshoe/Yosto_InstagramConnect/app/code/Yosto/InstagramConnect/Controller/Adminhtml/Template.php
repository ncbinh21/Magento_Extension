<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Yosto\InstagramConnect\Model\TemplateFactory;

/**
 * Class Group
 * @package Yosto\InstagramConnect\Controller\Adminhtml
 */
abstract class Template extends Action
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * Group constructor.
     * @param Action\Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        TemplateFactory $templateFactory,
        ProductFactory $productFactory
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_templateFactory = $templateFactory;
        $this->_productFactory = $productFactory;

        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Yosto_InstagramConnect::yosto_instagram_connect');
    }
}
