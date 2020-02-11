<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Model\ResourceModel\Status;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_objectManager;
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Amasty\OrderStatus\Model\ResourceModel\Status $resource = null,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, null, $resource);
        $this->_objectManager = $objectManager;
        $this->_scopeConfig = $scopeConfig;
    }

    protected function _construct()
    {
        $this->_init(
            'Amasty\OrderStatus\Model\Status',
            'Amasty\OrderStatus\Model\ResourceModel\Status'
        );
    }

    public function toOptionArray()
    {
        $hideState = false;

        if ($this->_scopeConfig->getValue('amostatus/general/hide_state')) {
            $hideState = true;
        }

        /** @var \Magento\Sales\Model\Order\Config $orderConfig */
        $orderConfig = $this->_objectManager->get('Magento\Sales\Model\Order\Config');
        $statuses = $orderConfig->getStatuses();
        if ($this->getSize() > 0) {
            foreach ($orderConfig->getStates() as $state => $node) {
                $label = trim((string)$node->getText());
                $states[$label] = $state;
            }
            foreach ($states as $stateLabel => $state) {
                foreach ($this as $status) {
                    if ($status->getData('is_active') && !$status->getData('is_system')) {
                        // checking if we should apply status to the current state
                        $parentStates = [];
                        if ($status->getParentState()) {
                            $parentStates = explode(',', $status->getParentState());
                        }
                        if (!$parentStates || in_array($state, $parentStates)) {
                            $elementName = $state . '_' . $status->getAlias();
                            $statuses[$elementName] = ($hideState ? '' : $stateLabel . ': ') . __($status->getStatus());
                        }
                    }
                }
            }
        }

        foreach ($statuses as $value => $label) {
            $statuses[] = ['value' => $value, 'label' => $label];
            unset($statuses[$value]);
        }

        return $statuses;
    }
}
