<?php


namespace Forix\ProductWizard\Model;

use Forix\ProductWizard\Api\Data\GroupInterface;

/**
 * Class Group
 * @method  \Forix\ProductWizard\Model\ResourceModel\Group getResource()
 * @package Forix\ProductWizard\Model
 */
class Group extends \Magento\Framework\Model\AbstractModel implements GroupInterface
{

    protected $_eventPrefix = 'forix_productwizard_group';

    /**
     * @return \Forix\ProductWizard\Model\ResourceModel\GroupItem\Collection
     */
    public function getGroupItemCollection(){
        return $this->getResource()->getGroupItemsBy($this);
    }
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Forix\ProductWizard\Model\ResourceModel\Group');
    }

    /**
     * Get group_id
     * @return string
     */
    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID);
    }

    /**
     * Set group_id
     * @param string $groupId
     * @return \Forix\ProductWizard\Api\Data\GroupInterface
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::GROUP_ID, $groupId);
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Set title
     * @param string $title
     * @return \Forix\ProductWizard\Api\Data\GroupInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get step_id
     * @return string
     */
    public function getStepId()
    {
        return $this->getData(self::STEP_ID);
    }

    /**
     * Set step_id
     * @param string $stepId
     * @return \Forix\ProductWizard\Api\Data\GroupInterface
     */
    public function setStepId($stepId)
    {
        return $this->setData(self::STEP_ID, $stepId);
    }
}
