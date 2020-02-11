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



namespace Mirasvit\EmailReport\Service\Registrars;


use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Api\Data\OrderInterface;
use Mirasvit\Email\Api\Repository\QueueRepositoryInterface;
use Mirasvit\EmailReport\Api\Repository\OrderRepositoryInterface;
use Mirasvit\EmailReport\Api\Service\RegistrarInterface;

class OrderRegistrar implements RegistrarInterface
{
    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * OrderRegistrar constructor.
     *
     * @param QueueRepositoryInterface $queueRepository
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        QueueRepositoryInterface $queueRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->queueRepository = $queueRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function register(AbstractModel $model, $queueId)
    {
        if ($model instanceof OrderInterface) {
            $queue = $this->queueRepository->get($queueId);
            if ($queue->getId()
                && $queue->getTrigger()
                && $queue->getTrigger()->getId()
            ) {
                $order = $this->orderRepository->create()
                    ->setTriggerId($queue->getTriggerId())
                    ->setQueueId($queueId)
                    ->setParentId($model->getId());

                $this->orderRepository->saveIfNotExist($order);
            }
        }
    }
}
