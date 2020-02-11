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
use Magento\Review\Model\Review;
use Mirasvit\Email\Api\Repository\QueueRepositoryInterface;
use Mirasvit\EmailReport\Api\Repository\ReviewRepositoryInterface;
use Mirasvit\EmailReport\Api\Service\RegistrarInterface;

class ReviewRegistrar implements RegistrarInterface
{
    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;
    /**
     * @var ReviewRepositoryInterface
     */
    private $reviewRepository;

    /**
     * ReviewRegistrar constructor.
     *
     * @param ReviewRepositoryInterface $reviewRepository
     * @param QueueRepositoryInterface  $queueRepository
     */
    public function __construct(
        ReviewRepositoryInterface $reviewRepository,
        QueueRepositoryInterface $queueRepository
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->queueRepository = $queueRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function register(AbstractModel $model, $queueId)
    {
        if ($model instanceof Review) {
            $queue = $this->queueRepository->get($queueId);
            if ($queue->getId()
                && $queue->getTrigger()
                && $queue->getTrigger()->getId()
            ) {
                $review = $this->reviewRepository->create()
                    ->setTriggerId($queue->getTriggerId())
                    ->setQueueId($queueId)
                    ->setParentId($model->getId());

                $this->reviewRepository->saveIfNotExist($review);
            }
        }
    }
}
