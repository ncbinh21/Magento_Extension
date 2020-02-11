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
use Mirasvit\EmailReport\Api\Data\OpenInterface;
use Mirasvit\EmailReport\Api\Data\OpenInterfaceFactory;
use Mirasvit\EmailReport\Model\ResourceModel\Open\CollectionFactory;
use Mirasvit\EmailReport\Api\Repository\OpenRepositoryInterface;

class OpenRepository implements OpenRepositoryInterface
{
    /**
     * @var OpenInterface[]
     */
    private $openRegistry = [];
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * OpenRepository constructor.
     *
     * @param OpenInterfaceFactory $openFactory
     * @param CollectionFactory    $collectionFactory
     * @param EntityManager        $entityManager
     */
    public function __construct(
        OpenInterfaceFactory $openFactory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->openFactory = $openFactory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        if (isset($this->openRegistry[$id])) {
            return $this->openRegistry[$id];
        }

        /** @var OpenInterface $open */
        $open = $this->openFactory->create();
        $open = $this->entityManager->load($open, $id);

        if ($open->getId()) {
            $this->openRegistry[$id] = $open;
        }

        if (!$open->getId()) {
            throw NoSuchEntityException::singleField(OpenInterface::ID, $id);
        }

        return $open;
    }

    /**
     * {@inheritDoc}
     */
    public function save(OpenInterface $open)
    {
        $dateTime = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        if (!$open->getId()) {
            $open->setCreatedAt($dateTime);
        }

        return $this->entityManager->save($open);
    }

    /**
     * {@inheritDoc}
     */
    public function saveIfNotExist(OpenInterface $open)
    {
        $size = $this->getCollection()
            ->addFieldToFilter(OpenInterface::QUEUE_ID, $open->getQueueId())
            ->addFieldToFilter(OpenInterface::SESSION_ID, $open->getSessionId())
            ->getSize();

        if (!$size) {
            $this->save($open);
        }

        return $open;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(OpenInterface $open)
    {
        return $this->entityManager->delete($open);
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
        return $this->openFactory->create();
    }
}
