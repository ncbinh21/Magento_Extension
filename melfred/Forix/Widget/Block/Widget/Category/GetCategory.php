<?php

namespace Forix\Widget\Block\Widget\Category;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Store\Model\StoreManagerInterface;
use Forix\Base\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class GetCategory extends  Template implements BlockInterface
{

	protected $_template = "category/get_category.phtml";
	protected $_storeManager;
	protected $_helper;
	protected $_categoryCollectionFactory;

	public function __construct(
		Template\Context $context,
		StoreManagerInterface $storeManager,
        Data $helper,
        CollectionFactory $collectionFactory,
        array $data = []
	)
	{
		parent::__construct($context, $data);
		$this->_storeManager  = $storeManager;
		$this->_helper = $helper;
		$this->_categoryCollectionFactory = $collectionFactory;
	}

    public function getCacheLifetime(){
	    //Enable cache life time for get Category
	    return 86400;
    }
	public function getCategory()
	{
		$ids = $this->_helper->getConfigValue('forix_catalog/feature_category/ids');
		$ids = explode(",", $ids);
		$categories = $this->_categoryCollectionFactory->create()
			->addAttributeToSelect(['name', 'url', 'img', 'icon_image','short_desc'])
			->addAttributeToFilter('entity_id',array('nin' => $ids))
			->addIsActiveFilter()
			->addAttributeToSort('position','ASC')
			->setStore($this->_storeManager->getStore());

		return $categories;
	}

}