<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Storelocator
 */


namespace Amasty\Storelocator\Model;
use Amasty\Storelocator\Model\Rule\Condition\Product\Combine;
use Magento\SalesRule\Model\Rule\Condition\Combine as ConditionCombine;

class Location extends \Magento\Rule\Model\AbstractModel
{
    /**
     * Store rule actions model
     *
     * @var \Magento\Rule\Model\Action\Collection
     */
    protected $_actions;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
     */
    protected $_condProdCombineF;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    protected $serializer;

    protected $_combine;

    protected $_conditionCombine;

    /**
     * Location constructor.
     *
     * @param \Magento\Framework\Model\Context                     $context
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Framework\Data\FormFactory                  $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager
     * @param array                                                $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Base\Model\Serializer $serializer,
		Combine $combine,
        ConditionCombine $conditionCombine,
        array $data = []
    ) {

        $this->_storeManager = $storeManager;
        $this->serializer = $serializer;
        $this->_combine   = $combine;
        $this->_conditionCombine = $conditionCombine;
        parent::__construct(
            $context, $registry, $formFactory, $localeDate, null, null, $data
        );
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Storelocator\Model\ResourceModel\Location');
    }

    public function getConditionsInstance()
    {
    	$conditions = $this->_conditionCombine;
        return $conditions;
    }

    public function getActionsInstance()
    {
        $actions = $this->_combine;
	    return $actions;
    }

    /**
     * Retrieve rule actions model
     *
     * @return \Magento\Rule\Model\Action\Collection
     */
    public function getActions()
    {
        if (!$this->_actions) {
            $this->_resetActions();
        }

        // Load rule actions if it is applicable
        if ($this->hasActionsSerialized()) {
            $actions = $this->getActionsSerialized();
            if (!empty($actions)) {
                $actions = $this->serializer->unserialize($actions);
                if (is_array($actions) && !empty($actions)) {
                    $this->_actions->loadArray($actions);
                }
            }
            $this->unsActionsSerialized();
        }

        return $this->_actions;
    }

    /**
     * Reset rule actions
     *
     * @param null|\Magento\Rule\Model\Action\Collection $actions
     * @return $this
     */
    protected function _resetActions($actions = null)
    {
        if (null === $actions) {
            $actions = $this->getActionsInstance();
        }
        
        $actions->setRule($this)->setId('1')->setPrefix('actions');
        $this->setActions($actions);

        return $this;
    }

    /**
     * Set rule actions model
     *
     * @param \Magento\Rule\Model\Action\Collection $actions
     * @return $this
     */
    public function setActions($actions)
    {
        $this->_actions = $actions;
        return $this;
    }

    public function activate()
    {
        $this->setStatus(1);
        $this->save();
        return $this;
    }

    public function inactivate()
    {
        $this->setStatus(0);
        $this->save();
        return $this;
    }

    /**
     * Set flags for saving new location
     */
    public function setModelFlags()
    {
        $this->getResource()->setResourceFlags();
    }
}
