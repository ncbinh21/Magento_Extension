<?php

namespace Forix\AdvancedAttribute\Block\Frontend;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class AttributeImage extends Template implements BlockInterface
{

    protected $_template = "widget/AttributeImage.phtml";
    protected $_optionCollection;
    protected $_imageBaseURL;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Forix\AdvancedAttribute\Model\ResourceModel\Option\CollectionFactory $optionCollection,
        \Forix\AdvancedAttribute\Model\Image $image,
        array $data = []
    )
    {
        $this->_optionCollection = $optionCollection;
        $this->_imageBaseURL = $image->getBaseUrl();
        parent::__construct($context, $data);

    }

    public function getRootUrl() {
        return $this->_imageBaseURL;
    }

    public function getAllbanners($attributeId)
    {
        $collection = $this->_optionCollection->create()->addFieldToFilter('attribute_id', ['eq' => $attributeId]);
        return $collection->getData();
    }

}