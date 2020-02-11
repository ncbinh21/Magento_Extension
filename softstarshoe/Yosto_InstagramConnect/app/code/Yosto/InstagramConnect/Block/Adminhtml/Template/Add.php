<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Block\Adminhtml\Template;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Magento\Catalog\Model\ProductFactory;
use Yosto\InstagramConnect\Helper\Constant;
use Yosto\InstagramConnect\Helper\InstagramClient;
use Yosto\InstagramConnect\Helper\InstagramConnectHelper;

/**
 * Class Edit
 * @package Yosto\InstagramConnect\Block\Adminhtml\Template
 */
class Add extends Container
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    protected $_instagramConnectHelper;

    protected $_productFactory;

    /**
     * Edit constructor.
     * @param Context $context
     * @param array $data
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        array $data,
        Registry $coreRegistry,
        InstagramClient $instagramConnectHelper,
        ProductFactory $productFactory
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_instagramConnectHelper = $instagramConnectHelper;
        $this->_productFactory   = $productFactory;
        parent::__construct($context, $data);
    }

    /**
     * Override constructor
     */
    protected function _construct()
    {
        $this->_objectId = 'template_id';
        $this->_controller = 'adminhtml_template';
        $this->_blockGroup = 'Yosto_InstagramConnect';
        $this->_template = 'Yosto_InstagramConnect::add.phtml';

        parent::_construct();

        $templateId = $this->getRequest()->getParam($this->_objectId);

        if (!$templateId) {
            $this->buttonList->remove('delete');
        }
    }

    function getProductInstagramMedia() {
        $productCollection = $this->_productFactory->create()->getCollection()->addAttributeToFilter(Constant::INSTAGRAM_HASH_TAG,[Constant::QUERY_REQUIRE_NOT_EQUAL => '']);
        $listMedia = [];
        foreach($productCollection as $product){
            $listMedia[] = [
                $product->getId(),
                $this->_instagramConnectHelper->getTagMedia($product->getInstagramHashTag())->data
            ];
        }
        return $listMedia;
    }
}