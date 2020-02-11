<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Model\Order\Plugin;

class Config extends \Magento\Sales\Model\Order\Config
{
    protected $_objectManager;
    protected $_scopeConfig;

    public function __construct(
        \Magento\Sales\Model\Order\StatusFactory $orderStatusFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $orderStatusCollectionFactory,
        \Magento\Framework\App\State $state,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_objectManager = $objectManager;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($orderStatusFactory, $orderStatusCollectionFactory, $state);
    }

    public function aroundGetStateStatuses($subject, $proceed, $stateToGetFor, $addLabels = true)
    {
        $hideState = ($this->_scopeConfig->getValue('amostatus/general/hide_state'))? true: false;

        /** @var \Amasty\OrderStatus\Model\ResourceModel\Status\Collection $statusesCollection */
        $statusesCollection = $this->_objectManager->get('Amasty\OrderStatus\Model\ResourceModel\Status\Collection');

        $statuses = $proceed($stateToGetFor);

        if ($statusesCollection->getSize() > 0) {
            $configStatuses = parent::getStates();
            if(!is_array($stateToGetFor)) {
                $stateToGetFor = [$stateToGetFor];
            }

            foreach ($stateToGetFor as $getFor) {
                if(array_key_exists($getFor, $configStatuses)){
                    $node = $configStatuses[$getFor];
                    $stateLabel = trim((string)$node->getText());
                    $state = $getFor;

                    foreach ($statusesCollection as $status) {
                        if ($status->getData('is_active') && !$status->getData('is_system')) {
                            // checking if we should apply status to the current state
                            $parentStates = [];
                            if ($status->getParentState()) {
                                $parentStates = explode(',', $status->getParentState());
                            }
                            if (!$parentStates || in_array($state, $parentStates)) {
                                $elementName = $state . '_' . $status->getAlias();
                                if ($addLabels) {
                                    $statuses[$elementName] = ($hideState ? '' : $stateLabel . ': ') . __($status->getStatus());
                                } else {
                                    $statuses[] = $elementName;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $statuses;
    }

    public function aroundGetStatusLabel($subject, $procede, $code)
    {
        $hideState = ($this->_scopeConfig->getValue('amostatus/general/hide_state'))? true: false;

        $statusLabel = $procede($code);
        if (empty($statusLabel) || (is_object($statusLabel) && !$statusLabel->getText())) {
            /** @var \Amasty\OrderStatus\Model\ResourceModel\Status\Collection $statusesCollection */
            $statusesCollection = $this->_objectManager->get('Amasty\OrderStatus\Model\ResourceModel\Status\Collection');
            if ($statusesCollection->getSize() > 0) {
                foreach (parent::getStates() as $state => $node) {
                    $stateLabel = trim((string)$node->getText());

                    foreach ($statusesCollection as $status) {
                        if ($status->getData('is_active') && !$status->getData('is_system')) {
                            // checking if we should apply status to the current state
                            $parentStates = array();
                            if ($status->getParentState()) {
                                $parentStates = explode(',', $status->getParentState());
                            }
                            if (!$parentStates || in_array($state, $parentStates)) {
                                $elementName = $state . '_' . $status->getAlias();
                                if ($code == $elementName) {
                                    $statusLabel = ($hideState ? '' : $stateLabel . ': ') . __($status->getStatus());
                                    break(2);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $statusLabel;
    }

    public function afterGetVisibleOnFrontStatuses($subject, $result)
    {
        /** @var \Amasty\OrderStatus\Model\Status $amastyStatusModel */
        $amastyStatusModel = $this->_objectManager->get('Amasty\OrderStatus\Model\Status');

        $ourStatuses = $amastyStatusModel->getAllStateStatuses();

        $result = array_merge($result, $ourStatuses);

        return $result;
    }
}
