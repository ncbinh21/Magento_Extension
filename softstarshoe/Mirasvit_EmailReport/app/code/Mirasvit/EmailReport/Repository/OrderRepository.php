<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email-report
 * @version   1.0.5
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Repository;


use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Mirasvit\EmailReport\Api\Data\OrderInterface;
use Mirasvit\EmailReport\Api\Data\OrderInterfaceFactory;
use Mirasvit\EmailReport\Model\ResourceModel\Order\CollectionFactory;
use Mirasvit\EmailReport\Api\Repository\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * @var OrderInterface[]
     */
    private $orderRegistry = [];
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * OrderRepository constructor.
     *
     * @param OrderInterfaceFactory $orderFactory
     * @param CollectionFactory    $collectionFactory
     * @param EntityManager        $entityManager
     */
    public function __construct(
        OrderInterfaceFactory $orderFactory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->orderFactory = $orderFactory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        if (isset($this->orderRegistry[$id])) {
            return $this->orderRegistry[$id];
        }

        /** @var OrderInterface $order */
        $order = $this->orderFactory->create();
        $order = $this->entityManager->load($order, $id);

        if ($order->getId()) {
            $this->orderRegistry[$id] = $order;
        }

        if (!$order->getId()) {
            throw NoSuchEntityException::singleField(OrderInterface::ID, $id);
        }

        return $order;
    }

    /**
     * {@inheritDoc}
     */
    public function save(OrderInterface $order)
    {
        $dateTime = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        if (!$order->getId()) {
            $order->setCreatedAt($dateTime);
        }

        return $this->entityManager->save($order);
    }

    /**
     * {@inheritDoc}
     */
    public function saveIfNotExist(OrderInterface $order)
    {
        $size = $this->getCollection()
            ->addFieldToFilter(OrderInterface::QUEUE_ID, $order->getQueueId())
            ->addFieldToFilter(OrderInterface::PARENT_ID, $order->getParentId())
            ->getSize();

        if (!$size) {
            $this->save($order);
        }

        return $order;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(OrderInterface $order)
    {
        return $this->entityManager->delete($order);
    }

    /**
     * {@inheritDoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->orderFactory->create();
    }
}
