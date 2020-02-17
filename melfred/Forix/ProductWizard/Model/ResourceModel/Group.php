<?php


namespace Forix\ProductWizard\Model\ResourceModel;
use Forix\ProductWizard\Model\GroupItem;

class Group extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected $_date;
    /**
     * @var GroupItem
     */
    protected $_groupItemInstance;

    /**
     * @var \Forix\ProductWizard\Model\GroupItemFactory
     */
    protected $_groupItemFactory;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Forix\ProductWizard\Model\GroupItemFactory $groupItemFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        $connectionName = null)
    {
        $this->_date = $date;
        $this->_groupItemFactory = $groupItemFactory;
        parent::__construct($context, $connectionName);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getId()) {
            $object->setCreatedAt($this->_date->gmtDate());
        }
        $object->setUpdatedAt($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * @param \Forix\ProductWizard\Model\Group $group
     * @return \Forix\ProductWizard\Model\ResourceModel\GroupItem\Collection
     */
    public function getGroupItemsBy($group){
        return $this->getGroupItemInstance()->getGroupItemsBy($group);
    }
    
    /**
     * @return GroupItem
     */
    public function getGroupItemInstance()
    {
        if (!isset($this->_groupItemInstance)) {
            $this->_groupItemInstance = $this->_groupItemFactory->create();
        }
        return $this->_groupItemInstance;
    }
    
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_productwizard_group', 'group_id');
    }
}
