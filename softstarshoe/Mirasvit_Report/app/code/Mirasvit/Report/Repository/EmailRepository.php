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
 * @package   mirasvit/module-report
 * @version   1.2.27
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Report\Repository;

use Magento\Framework\Exception\NoSuchEntityException;
use Mirasvit\Report\Api\Repository\Email\BlockRepositoryInterface;
use Mirasvit\Report\Model\EmailFactory;
use Mirasvit\Report\Model\ResourceModel\Email\CollectionFactory as EmailCollectionFactory;
use Mirasvit\Report\Api\Data\EmailInterface;
use Mirasvit\Report\Api\Repository\EmailRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;

class EmailRepository implements EmailRepositoryInterface
{
    /**
     * @var EmailFactory
     */
    protected $emailFactory;

    /**
     * @var EmailCollectionFactory
     */
    protected $emailCollectionFactory;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    private $repositoryPool = [];

    /**
     * @var array
     */
    private $blockPool = [];

    public function __construct(
        EmailFactory $emailFactory,
        EmailCollectionFactory $emailCollectionFactory,
        ObjectManagerInterface $objectManager,
        array $repositoryPool = []
    ) {
        $this->emailFactory = $emailFactory;
        $this->emailCollectionFactory = $emailCollectionFactory;
        $this->objectManager = $objectManager;
        $this->repositoryPool = $repositoryPool;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->emailCollectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $email = $this->emailFactory->create();
        $email->load($id);

        if (!$email->getId()) {
            throw NoSuchEntityException::singleField('email_id', $id);
        }

        return $email;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $this->getBlocks();

        return $this->emailFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(EmailInterface $email)
    {
        /** @var \Mirasvit\Report\Model\Email $email */
        $email->delete();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function save(EmailInterface $email)
    {
        /** @var \Mirasvit\Report\Model\Email $email */

        if ($email->getData('blocks')) {
            $email->setData(EmailInterface::BLOCKS_SERIALIZED, \Zend_Json::encode($email->getData('blocks')));
        }

        return $email->save();
    }

    public function getBlocks()
    {
        if (count($this->blockPool)) {
            return $this->blockPool;
        }

        foreach ($this->repositoryPool as $repositoryClass) {
            /** @var BlockRepositoryInterface $repository */
            $repository = $this->objectManager->get($repositoryClass);

            if (!($repository instanceof BlockRepositoryInterface)) {
                throw new \Exception(__("Class $repositoryClass should implement EmailBlocksRepositoryInterface."));
            }
            foreach ($repository->getBlocks() as $identifier => $block) {
                $this->blockPool[$identifier] = [
                    'name'       => $block,
                    'repository' => $repository,
                ];
            }
        }

        return $this->blockPool;
    }
}