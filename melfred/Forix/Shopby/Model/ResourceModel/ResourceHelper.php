<?php

namespace Forix\Shopby\Model\ResourceModel;

class ResourceHelper
{
    protected $_storeManager;
    protected $_resource;
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource)
    {
        $this->_resource = $resource;
        $this->_storeManager = $storeManager;
    }

    protected $select;

	public function getOptionIdByLabel($value)
	{
		$adapter = $this->_resource->getConnection();
		$this->select = $adapter->select()
			->from(['mainTb'=>$this->_resource->getTableName('eav_attribute_option_value')])
			->where('mainTb.value = (?)', $value);
		$query = $adapter->query($this->select);
		$item  = $query->fetch();
		return $item;
	}

	public function getOptionIdByCode($code)
	{
		$adapter = $this->_resource->getConnection();
		$this->select = $adapter->select()
			->from(['mainTb'=>$this->_resource->getTableName('eav_attribute')])
			->where('mainTb.attribute_code = (?)', $code);
		$query = $adapter->query($this->select);
		$item  = $query->fetch();
		return $item;
	}

	public function getFetchOptionById($id)
	{
		$adapter = $this->_resource->getConnection();
		$this->select = $adapter->select()
			->from(['mainTb'=>$this->_resource->getTableName('forix_attribute_option_banner')])
			->where('mainTb. option_id= (?)', $id);
		$query = $adapter->query($this->select);
		$item  = $query->fetch();
		return $item;
	}

	public function getBanner($id)
	{
	    /*
		$res = $this->getFetchOptionById($id);
		if (isset($res['mb_oem'])) {
			$adapter = $this->_resource->getConnection();
			$this->select = $adapter->select()
				->from(['mainTb'=>$this->_resource->getTableName('forix_attribute_option_banner')])
				->where('mainTb. option_id= (?)', $res['mb_oem']);
			$query = $adapter->query($this->select);
			$item  = $query->fetch();
			return $item;
		}
	    */
        $adapter = $this->_resource->getConnection();
        $this->select = $adapter->select()
            ->from(['mainTb'=>$this->_resource->getTableName('forix_attribute_option_banner')])
            ->where('mainTb.option_id= (?)', $id);
        $query = $adapter->query($this->select);
        $item  = $query->fetch();

        return $item;
	}

	public function getOptionById($id)
	{
		$adapter = $this->_resource->getConnection();
		$this->select = $adapter->select()
			->from(['mainTb'=>$this->_resource->getTableName('eav_attribute_option_value')])
			->where('mainTb.option_id = (?)', $id);
		$query = $adapter->query($this->select);
		$item  = $query->fetch();
		return $item;
	}

	public function getOptionValueByIds($ids) {
		$adapter = $this->_resource->getConnection();
		$this->select = $adapter->select()
			->from(['eao'=>$this->_resource->getTableName('eav_attribute_option')])
			->joinLeft(['eav_default'=>'eav_attribute_option_value'],'eao.option_id = eav_default.option_id AND eav_default.store_id = 0',[])
			->joinLeft(['eav'=>'eav_attribute_option_value'],"eao.option_id = eav.option_id AND eav.store_id={$this->_storeManager->getStore()->getId()}",[])
			->where('eao.option_id IN (?)', $ids)
			->order("sort_order ASC");
        $expression = $adapter->getCheckSql(
            $adapter->getIfNullSql("eav.value_id", -1) . ' > 0',
            "eav.value",
            "eav_default.value"
        );
        $this->select->columns(['value' => $expression]);
		$query = $adapter->query($this->select);
		$item  = $query->fetchAll();
		return $item;
	}
}