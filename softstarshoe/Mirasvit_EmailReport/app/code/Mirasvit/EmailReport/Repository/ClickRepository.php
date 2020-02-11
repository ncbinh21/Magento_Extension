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
use Mirasvit\EmailReport\Api\Data\ClickInterface;
use Mirasvit\EmailReport\Api\Data\ClickInterfaceFactory;
use Mirasvit\EmailReport\Model\ResourceModel\Click\CollectionFactory;
use Mirasvit\EmailReport\Api\Repository\ClickRepositoryInterface;

class ClickRepository implements ClickRepositoryInterface
{
    /**
     * @var ClickInterface[]
     */
    private $clickRegistry = [];
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ClickRepository constructor.
     *
     * @param ClickInterfaceFactory $clickFactory
     * @param CollectionFactory     $collectionFactory
     * @param EntityManager         $entityManager
     */
    public function __construct(
        ClickInterfaceFactory $clickFactory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->clickFactory = $clickFactory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        if (isset($this->clickRegistry[$id])) {
            return $this->clickRegistry[$id];
        }

        /** @var ClickInterface $click */
        $click = $this->clickFactory->create();
        $click = $this->entityManager->load($click, $id);

        if ($click->getId()) {
            $this->clickRegistry[$id] = $click;
        }

        if (!$click->getId()) {
            throw NoSuchEntityException::singleField(ClickInterface::ID, $id);
        }

        return $click;
    }

    /**
     * {@inheritDoc}
     */
    public function save(ClickInterface $click)
    {
        $dateTime = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        if (!$click->getId()) {
            $click->setCreatedAt($dateTime);
        }

        return $this->entityManager->save($click);
    }

    /**
     * {@inheritDoc}
     */
    public function saveIfNotExist(ClickInterface $click)
    {
        $size = $this->getCollection()
            ->addFieldToFilter(ClickInterface::QUEUE_ID, $click->getQueueId())
            ->addFieldToFilter(ClickInterface::SESSION_ID, $click->getSessionId())
            ->getSize();

        if (!$size) {
            $this->save($click);
        }

        return $click;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(ClickInterface $click)
    {
        return $this->entityManager->delete($click);
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
        return $this->clickFactory->create();
    }
}
