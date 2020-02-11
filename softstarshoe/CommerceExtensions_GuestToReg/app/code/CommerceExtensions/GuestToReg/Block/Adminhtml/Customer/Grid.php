<?php
/**
 * Copyright © 2016 CommerceExtensions . All rights reserved.
 */
namespace CommerceExtensions\GuestToReg\Block\Adminhtml\Customer;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {
	 

	protected $_coreRegistry;
	
	protected $_collectionFactory;
	
	protected $_orderConfig;
	
	protected $_group;
	 
	/**
	* [__construct description]
	* @param \Magento\Backend\Block\Template\Context $context [description]
	* @param \Magento\Backend\Helper\Data $backendHelper [description]
	* @param \Magento\Framework\Registry $coreRegistry [description]
	* @param array $data [description]
	*/
	public function __construct(
		\Magento\Backend\Block\Template\Context $context, 
		\Magento\Backend\Helper\Data $backendHelper,
		\Magento\Sales\Model\Order\Config $orderConfig,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
		\Magento\Customer\Model\Group $group,
		\Magento\Framework\App\ResourceConnection $resource,
		\Magento\Framework\Registry $coreRegistry
		) {
		$this->_coreRegistry = $coreRegistry;
		$this->_collectionFactory = $collectionFactory;
		$this->_group = $group;
		$this->_orderConfig = $orderConfig;
		$this->_resource = $resource;
		parent::__construct($context, $backendHelper);
	}
	 
	protected function _construct() {
        parent::_construct();
        $this->setId('customVariablesGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
	}
	 
	protected function _prepareCollection() {
	
	
	 	$collection = $this->_collectionFactory->create(); 
        $collection->getSelect()->join(array('sfoab' => $this->_resource->getTableName('sales_order_address')), 'main_table.billing_address_id=sfoab.entity_id',
            array('bill_firstname' => 'sfoab.firstname', 'bill_lastname' => 'sfoab.lastname'));
			
        $collection->getSelect()->join(array('sfoas' => $this->_resource->getTableName('sales_order_address')), 'main_table.shipping_address_id=sfoas.entity_id',
            array('ship_firstname' => 'sfoas.firstname', 'ship_lastname' => 'sfoas.lastname'));
			
		$collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('customer_id', array('null' => true));
		
        $this->setCollection($collection);
        return parent::_prepareCollection();
	}
	 
	/**
	* @return $this
	*/
	protected function _prepareColumns() {
	
		$this->addColumn(
			'real_order_id', [
			'header' => __('Order #'),
			'index' => 'increment_id',
			]
		);
		
		$this->addColumn(
			'created_at', [
			'header' => __('Purchased On'),
			'type' => 'datetime',
			'index' => 'created_at',
			]
		);
		
		$this->addColumn(
			'customer_firstname', [
			'header' => __('First Name'),
			'index' => 'customer_firstname',
			]
		);
		
		$this->addColumn(
			'customer_lastname', [
			'header' => __('Last Name'),
			'index' => 'customer_lastname',
			]
		);
		
		$this->addColumn(
			'bill_firstname', [
			'header' => __('Bill First Name'),
			'index' => 'bill_firstname',
            'filter_index' => 'sfoab.firstname',
			]
		);
		
		$this->addColumn(
			'bill_lastname', [
			'header' => __('Bill Last Name'),
			'index' => 'bill_lastname',
            'filter_index' => 'sfoab.lastname',
			]
		);
		
		$this->addColumn(
			'ship_firstname', [
			'header' => __('Ship First Name'),
			'index' => 'ship_firstname',
            'filter_index' => 'sfoas.firstname',
			]
		);
		
		$this->addColumn(
			'ship_lastname', [
			'header' => __('Ship Last Name'),
			'index' => 'ship_lastname',
            'filter_index' => 'sfoas.lastname',
			]
		);
		
		$this->addColumn(
			'customer_email', [
			'header' => __('Email'),
			'index' => 'customer_email',
			]
		);
		
		$this->addColumn(
			'status', [
			'header' => __('Status'),
			'type'  => 'options',
			'index' => 'status',
			'options' => array_merge(array('',''),$this->_orderConfig->getStatuses()),
			]
		);
		
		$this->addColumn(
			'action',
			[
				'header' => __('Action'),
				'type' => 'action',
				'getter' => 'getId',
				'actions' => [
					[
						'caption' => __('View Order'),
						'url' => [
							'base' => 'sales/order/view',
						],
						'field' => 'order_id'
					]
				],
				'filter' => false,
				'sortable' => false,
				'index' => 'stores',
				'header_css_class' => 'col-action',
				'column_css_class' => 'col-action'
			]
		);
			
		return parent::_prepareColumns();
		}
		 
	/**
	* @return $this
	*/
	protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
		
        $groups = array();
        /** @var $customerGroupsCollection Mage_Customer_Model_Resource_Group_Collection */
        $customerGroupsCollection = $this->_group->getCollection()->load();
        $customerGroupsCollection->removeItemByKey(0);
        foreach ($customerGroupsCollection as $group)
        {
            /** @var $group Mage_Customer_Model_Group */
            $groups[] = array(
                'label' => $group->getCustomerGroupCode(),
                'value' => $group->getId()
            );
        }

		
		$this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Convert to customer'),
                'url' => $this->getUrl('*/*/massConvert', array('_current'=>true)),
				 'additional' => array(
					 'visibility' => array(
						 'name' => 'group_id',
						 'type' => 'select',
						 'class' => 'required-entry',
						 'label' => 'Customer Group'	,
						 'values' => $groups
					 )
				 )
            ]
        );
		 
	}
	 
	/**
	* @return string
	*/
	public function getGridUrl() {
		return $this->getUrl('*/*/index', array('_current'=>true));
	}
	 
	public function getRowUrl($row) {
		return false;
	}
 
}