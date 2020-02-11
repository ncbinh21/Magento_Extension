<?php

namespace Softstarshoes\Mods\Observer;

class SalesOrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $log;
    protected $logger;
    protected $groupRepository;
    protected $orderManagement;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement
    ) {
        $this->logger = $logger;
        $this->groupRepository = $groupRepository;
        $this->orderManagement = $orderManagement;
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/SalesOrderPlaceAfter.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->log = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $groupLookup = 'chin';
            if($order = $observer->getOrder()) {
                $group = $this->groupRepository->getById((int)$order->getCustomerGroupId());
                $groupName = $group->getCode();
                if(strpos(strtolower($groupName),$groupLookup) !== false) {
                    $order->hold()->save();
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return $this;
    }
}