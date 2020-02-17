<?php
namespace Forix\AdvancedAttribute\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Forix\AdvancedAttribute\Model\Status;

class BannerAttribute extends  \Magento\Framework\View\Element\Template
{

    protected $_template = 'Forix_AdvancedAttribute::banner.phtml';
    protected $_itemTemplate = 'Forix_AdvancedAttribute::slideritem.phtml';
    protected $_objectManager;
    protected $_bannerAttribute;
    protected $_coreRegistry;
    protected $_eavAttribute;
    protected $_attrOptionCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_objectManager = $objectManager;
        $this->_eavAttribute = $eavAttribute;
        $this->_coreRegistry = $coreRegistry;
        $this->_attrOptionCollectionFactory = $attrOptionCollectionFactory;
    }


    public function getBannerAttribute()
    {
        if (
            ($optionId = $this->getRequest()->getParam('first_filter_value')) &&
            ($attributeCode = $this->getRequest()->getParam('first_filter')) &&
            !in_array('catalogsearch_result_index', $this->getLayout()->getUpdate()->getHandles())
        ) {
            if (is_null($this->_bannerAttribute)) {
                $attributeId = $this->_eavAttribute->getIdByCode('catalog_product', $attributeCode);
                $collection = $this->_attrOptionCollectionFactory->create()
                    ->setPositionOrder('asc')
                    ->addFieldToFilter('main_table.attribute_id', $attributeId)
                    ->addFieldToFilter('main_table.option_id', $optionId)
                    ->setStoreFilter($this->_storeManager->getStore()->getId());
                $collection->getSelect()->joinLeft(
                    array('info' => 'forix_attribute_option_banner'),
                    'info.option_id = main_table.option_id',
                    array('image', 'logo_attribute', 'content', 'is_active', 'html_title')
                )->where("info.is_active = " . \Forix\AdvancedAttribute\Block\Adminhtml\Options\Edit\Tab\Main::IS_ACTIVE_YES);
                if ($collection->getSize() > 0) {
                    $this->_bannerAttribute = $collection->getFirstItem();
                } else {
                    $this->_bannerAttribute = false;
                }
            }
            return $this->_bannerAttribute;
        }
        return false;
    }

    public function getAttributeImageUrl($imagePath)
    {
        $modelImg = $this->_objectManager->get('Forix\AdvancedAttribute\Model\Image');
        return $modelImg->getBaseUrl() . $imagePath;
    }


    /**
     * get logo brand
     *
     */
    public function getLogoBrand()
    {
        $_product = $this->_coreRegistry->registry('current_product');
        if (!$_product) return null;

        $_brandCode = $this->_scopeConfig->getValue('brand/general/attributes', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $_optionId = $_product->getData($_brandCode);

        $collection = $this->_attrOptionCollectionFactory->create();
        $collection->addFieldToFilter('info.option_id', $_optionId)
            ->setStoreFilter($this->_storeManager->getStore()->getId());
        $collection->getSelect()->joinLeft(
            array('info' => 'forix_attribute_option_banner'),
            'info.option_id = main_table.option_id',
            array('logo_attribute', 'is_active')
        )->where("info.is_active = " . \Forix\AdvancedAttribute\Block\Adminhtml\Options\Edit\Tab\Main::IS_ACTIVE_YES);

        $logo = null;
        if ($collection->getSize() > 0) {
            $_valueImage = $collection->getFirstItem()->getData('logo_attribute');
            if ($_valueImage) {
                $logo = $this->_objectManager->get('Forix\Brand\Helper\Image')
                    ->init($_valueImage, ['width' => 85, 'height' => 85])
                    ->getUrl();
            }
        }
        return $logo;
    }

    /**
     * get atribute Features
     */
    public function getFeatures()
    {
        $_product = $this->_coreRegistry->registry('current_product');
        if (!$_product) return null;

        $_optionId = $_product->getData('features');

        $collection = $this->_attrOptionCollectionFactory->create();
        $collection->addFieldToFilter('info.option_id', array('in' => explode(",", $_optionId)))
            ->setStoreFilter($this->_storeManager->getStore()->getId());
        $collection->getSelect()->joinLeft(
            array('info' => 'forix_attribute_option_banner'),
            'info.option_id = main_table.option_id',
            array('icon_normal', 'is_active')
        )->where("info.is_active = " . \Forix\AdvancedAttribute\Block\Adminhtml\Options\Edit\Tab\Main::IS_ACTIVE_YES);

        if ($collection->getSize() > 0) {
            return $collection;
        }
        return null;
    }
}