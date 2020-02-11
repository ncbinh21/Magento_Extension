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
use Mirasvit\EmailReport\Api\Data\EmailInterface;
use Mirasvit\EmailReport\Api\Data\EmailInterfaceFactory;
use Mirasvit\EmailReport\Model\ResourceModel\Email\CollectionFactory;
use Mirasvit\EmailReport\Api\Repository\EmailRepositoryInterface;

class EmailRepository implements EmailRepositoryInterface
{
    /**
     * @var EmailInterface[]
     */
    private $emailRegistry = [];
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * EmailRepository constructor.
     *
     * @param EmailInterfaceFactory $emailFactory
     * @param CollectionFactory    $collectionFactory
     * @param EntityManager        $entityManager
     */
    public function __construct(
        EmailInterfaceFactory $emailFactory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->emailFactory = $emailFactory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        if (isset($this->emailRegistry[$id])) {
            return $this->emailRegistry[$id];
        }

        /** @var EmailInterface $email */
        $email = $this->emailFactory->create();
        $email = $this->entityManager->load($email, $id);

        if ($email->getId()) {
            $this->emailRegistry[$id] = $email;
        }

        if (!$email->getId()) {
            throw NoSuchEntityException::singleField(EmailInterface::ID, $id);
        }

        return $email;
    }

    /**
     * {@inheritDoc}
     */
    public function save(EmailInterface $email)
    {
        $dateTime = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        if (!$email->getId()) {
            $email->setCreatedAt($dateTime);
        }

        return $this->entityManager->save($email);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(EmailInterface $email)
    {
        return $this->entityManager->delete($email);
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
        return $this->emailFactory->create();
    }
}
