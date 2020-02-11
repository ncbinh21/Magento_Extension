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
use Mirasvit\EmailReport\Api\Data\ReviewInterface;
use Mirasvit\EmailReport\Api\Data\ReviewInterfaceFactory;
use Mirasvit\EmailReport\Model\ResourceModel\Review\CollectionFactory;
use Mirasvit\EmailReport\Api\Repository\ReviewRepositoryInterface;

class ReviewRepository implements ReviewRepositoryInterface
{
    /**
     * @var ReviewInterface[]
     */
    private $reviewRegistry = [];
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ReviewRepository constructor.
     *
     * @param ReviewInterfaceFactory $reviewFactory
     * @param CollectionFactory    $collectionFactory
     * @param EntityManager        $entityManager
     */
    public function __construct(
        ReviewInterfaceFactory $reviewFactory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        if (isset($this->reviewRegistry[$id])) {
            return $this->reviewRegistry[$id];
        }

        /** @var ReviewInterface $review */
        $review = $this->reviewFactory->create();
        $review = $this->entityManager->load($review, $id);

        if ($review->getId()) {
            $this->reviewRegistry[$id] = $review;
        }

        if (!$review->getId()) {
            throw NoSuchEntityException::singleField(ReviewInterface::ID, $id);
        }

        return $review;
    }

    /**
     * {@inheritDoc}
     */
    public function save(ReviewInterface $review)
    {
        $dateTime = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        if (!$review->getId()) {
            $review->setCreatedAt($dateTime);
        }

        return $this->entityManager->save($review);
    }

    /**
     * {@inheritDoc}
     */
    public function saveIfNotExist(ReviewInterface $review)
    {
        $size = $this->getCollection()
            ->addFieldToFilter(ReviewInterface::QUEUE_ID, $review->getQueueId())
            ->addFieldToFilter(ReviewInterface::PARENT_ID, $review->getParentId())
            ->getSize();

        if (!$size) {
            $this->save($review);
        }

        return $review;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(ReviewInterface $review)
    {
        return $this->entityManager->delete($review);
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
        return $this->reviewFactory->create();
    }
}
