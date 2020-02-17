<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\Distributor\Block\Adminhtml\Location;

use Magento\Store\Model\Store;

/**
 * @api
 * @since 100.0.2
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    protected $zipCodeFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Forix\Distributor\Model\ZipcodeFactory $zipCodeFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = [])
    {
        parent::__construct($context, $backendHelper, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->zipCodeFactory = $zipCodeFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('distributor_zipcodes');
        $this->setDefaultSort('zipcode_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        /**
         * @var $collection \Forix\Distributor\Model\ResourceModel\Zipcode\Collection
         */
        $collection = $this->zipCodeFactory->create()->getCollection();
        $collection->addFieldToSelect(
            '*'
        );

        $this->setCollection($collection);

        if ($this->getStoreLocation()) {
            $this->getCollection()->addFieldToFilter('distributor_id', [$this->getStoreLocation()->getId()]);
        }

        parent::_prepareCollection();

        return $this;
    }

    /**
     * Correctly process store_id filter
     * @param array|string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @return \Amasty\Storelocator\Model\Location|null
     */
    public function getStoreLocation()
    {
        return $this->_coreRegistry->registry('current_amasty_storelocator_location');
    }
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'zipcode_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'zipcode_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('distributor_zipcode', ['header' => __('ZipCode'), 'index' => 'zipcode']);
        $this->addColumn('distributor_city', ['header' => __('City'), 'index' => 'city']);
        $this->addColumn('distributor_country', ['header' => __('County'), 'index' => 'country']);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('forix_distributor/zipcode/grid', ['_current' => true]);
    }
}
