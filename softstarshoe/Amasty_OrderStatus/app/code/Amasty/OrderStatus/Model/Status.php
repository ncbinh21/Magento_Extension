<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Model;

use Magento\Framework\Model\AbstractModel;

class Status extends AbstractModel
{
    protected $_objectManager;

    protected function _construct()
    {
        $this->_init('Amasty\OrderStatus\Model\ResourceModel\Status');
    }

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $coreRegistry);
    }

    public function beforeSave()
    {
        parent::beforeSave();
        if (!$this->getAlias()) {
            $this->setAlias($this->_generateAlias($this->getStatus()));
        }
        return $this;
    }

    public function getAllStateStatuses()
    {
        $allStateStatuses = [];

        foreach ($this->getCollection() as $status) {
            $alias = $status->getAlias();
            $parentStates = explode(',', $status->getParentState());
            foreach ($parentStates as $state) {
                $allStateStatuses[] = $state . '_' . $alias;
            }
        }

        return $allStateStatuses;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        /** @var \Amasty\OrderStatus\Model\Template $template */
        $template = $this->_objectManager->get('Amasty\OrderStatus\Model\Template');
        $template->attachTemplates($this);
        return $this;
    }

    protected function _generateAlias($title)
    {
        $alias = trim(strtolower(preg_replace('@[^A-Za-z0-9_]@', '', $title)));
        if (strlen($alias) > 17) {
            $alias = substr($alias, 0, 17);
        }
        if (!$alias) {
            $alias = uniqid(rand(10, 99));
        }
        // need get unique alias
        $existStatuses = $this->getCollection();
        $existStatuses->addFieldToFilter('alias', $alias);
        while (0 < $existStatuses->getSize()) {
            unset($existStatuses);
            $alias = uniqid(rand(10, 99));
            $existStatuses = $this->getCollection();
            $existStatuses->addFieldToFilter('alias', $alias);
        }
        return $alias;
    }
}
