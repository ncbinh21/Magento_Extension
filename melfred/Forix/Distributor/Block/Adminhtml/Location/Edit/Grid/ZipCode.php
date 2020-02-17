<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 20/08/2018
 * Time: 14:01
 */

namespace Forix\Distributor\Block\Adminhtml\Location\Edit\Grid;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class ZipCode extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Forix\Distributor\Model\ZipcodeFactory
     */
    protected $_zipCodeFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Forix\Distributor\Model\ZipcodeFactory $zipCodeFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Forix\Distributor\Model\ZipcodeFactory $zipCodeFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    )
    {
        $this->_zipCodeFactory = $zipCodeFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
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
    }

    /**
     * @return \Amasty\Storelocator\Model\Location|null
     */
    public function getStoreLocation()
    {
        return $this->_coreRegistry->registry('current_amasty_storelocator_location');
    }


    public function getAdditionalJavaScript(){
        return "distributor_zipcodesJsObject.doFilter = function (){
            var filters = $$('#'+this.containerId+' .data-grid-filters input');
            var elements = [];
            for(var i in filters){
                if(filters[i].value && filters[i].value.length) elements.push(filters[i]);
            }
            if (!this.doFilterCallback || (this.doFilterCallback && this.doFilterCallback())) {
                this.reload(this.addVarToUrl(this.filterVar, Base64.encode(Form.serializeElements(elements))));
            }
        }";
    }
    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        /**
         * @var $collection \Forix\Distributor\Model\ResourceModel\Zipcode\Collection
         */
        $collection = $this->_zipCodeFactory->create()->getCollection();
        $collection->addFieldToSelect(
            '*'
        );

        $this->setCollection($collection);

        if ($this->getStoreLocation()) {
            $this->getCollection()->addFieldToFilter('distributor_id', [$this->getStoreLocation()->getId()]);
        }

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
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