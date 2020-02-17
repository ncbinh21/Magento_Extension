<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\AdvancedAttribute\Block\Adminhtml\Options;

/**
 * Adminhtml refunded report grid block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    const LIMIT_ROW_NUMBER = 1000;

    /**
     * @var \Forix\AdvancedAttribute\Model\ResourceModel\Option\CollectionFactory
     */
    protected $_bannerCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Forix\AdvancedAttribute\Model\ResourceModel\Option\CollectionFactory $bannerCollectionFactory,
        array $data = []
    ) {
        $this->_bannerCollectionFactory = $bannerCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
        $this->setDefaultLimit(self::LIMIT_ROW_NUMBER);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);

    }

    protected function _prepareCollection()
    {
        $attributeId     = $this->getRequest()->getParam('attrid');

        /** @var \Forix\AdvancedAttribute\Model\ResourceModel\Option\Collection $collection */
        $collection = $this->_bannerCollectionFactory->create();
        $collection->addFieldToFilter('attribute_id',$attributeId);
        $this->setCollection($collection);
        parent::_prepareCollection();
        $this->getCollection()->getSelect()->limit(self::LIMIT_ROW_NUMBER);

        return;

    }
    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'banner_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'banner_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'option_label',
            [
                'header' => __('Option Name'),
                'index' => 'option_id',
                'class' => 'xxx',
                'width' => '50px',
                'renderer' => 'Forix\AdvancedAttribute\Block\Adminhtml\Options\Renderer\Label',
            ]
        );
        $this->addColumn(
            'option_id',
            [
                'header' => __('Option Id'),
                'index' => 'option_id',
                'class' => 'xxx',
                'width' => '50px'
            ]
        );

        $this->addColumn(
            'image',
            [
                'header' => __('Banner'),
                'index' => 'image',
                'renderer' => 'Forix\AdvancedAttribute\Block\Adminhtml\Options\Renderer\Image',
            ]
        );
        $this->addColumn(
            'edit_attribute',
            [
                'header' => __('Action'),
                'renderer' => 'Forix\AdvancedAttribute\Block\Adminhtml\Options\Renderer\EditUrl',
            ]
        );



        return parent::_prepareColumns();
    }
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $attributeCode  = $this->getRequest()->getParam('attrcode');
        $attributeId    = $this->getRequest()->getParam('attrid');


        $this->setMassactionIdField('banner_id');
        $this->getMassactionBlock()->setFormFieldName('banner_option');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('attribute_banners/*/massDelete/rs/'. $attributeCode.'|'.$attributeId),
                'confirm' => __('Are you sure?'),
            ]
        );



        return $this;
    }
    /**
     * Grid row URL getter
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        $attributeCode  = $this->getRequest()->getParam('attrcode');

        return $this->getUrl('*/*/edit', ['id' => $row->getBannerId(),"attrid" => $row->getAttributeId(),"attrcode"=>$attributeCode]);
    }
}
