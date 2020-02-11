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
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Mirasvit\Email\Api\Data\TriggerChainInterface;
use Mirasvit\Email\Api\Repository\Trigger\ChainRepositoryInterface;
use Mirasvit\Email\Ui\Trigger\Form\DataProvider as TriggerDataProvider;
use Mirasvit\Email\Api\Data\TriggerInterface;
use Mirasvit\Email\Api\Repository\TriggerRepositoryInterface;
use Mirasvit\Email\Api\Data\TriggerInterfaceFactory;
use Mirasvit\Email\Model\ResourceModel\Trigger\CollectionFactory;

class TriggerRepository implements TriggerRepositoryInterface
{
    /**
     * @var TriggerInterface[]
     */
    private $triggerRegistry = [];

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TriggerInterfaceFactory
     */
    private $modelFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var ChainRepositoryInterface
     */
    private $chainRepository;

    public function __construct(
        ChainRepositoryInterface $chainRepository,
        EntityManager $entityManager,
        TriggerInterfaceFactory $modelFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->entityManager = $entityManager;
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
        $this->chainRepository = $chainRepository;
    }

    /**
     * {@inheritdoc}
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
        return $this->modelFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (isset($this->triggerRegistry[$id])) {
            return $this->triggerRegistry[$id];
        }

        $trigger = $this->create();
        $trigger = $this->entityManager->load($trigger, $id);
        //$trigger = $trigger->load($id);

        if ($trigger->getId()) {
            $this->triggerRegistry[$id] = $trigger;
        } else {
            return false;
        }

        return $trigger;
    }

    /**
     * {@inheritdoc}
     */
    public function save(TriggerInterface $model)
    {
        $model->setRuleSerialized(\Zend_Json_Encoder::encode($model->getRule() ? $model->getRule() : []));
        $model->setStoreIds($model->getStoreIds());
        $model->setCancellationEvent($model->getCancellationEvent());

        $this->entityManager->save($model);

        // Save chain only if notification saved from the edit page
        if ($model->getData(TriggerDataProvider::CHAIN) && !$model->getData('is_mass_action')) {
            $this->saveChain($model);
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(TriggerInterface $model)
    {
        return $this->entityManager->delete($model);
        //return $model->delete();
    }

    /**
     * Save chain for trigger
     *
     * @param \Mirasvit\Email\Api\Data\TriggerInterface $object
     *
     * @return $this
     *  @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function saveChain($object)
    {
        $collectionToDelete = $this->chainRepository->getCollection()
            ->addFieldToFilter(TriggerInterface::ID, $object->getId())
            ->addFieldToFilter(
                TriggerChainInterface::ID,
                ($object->hasData(TriggerDataProvider::CHAIN))
                    ? ['nin' => array_keys($object->getData(TriggerDataProvider::CHAIN))]
                    : ['like' => '%']
            );

        foreach ($collectionToDelete as $item) {
            $this->chainRepository->delete($item);
        }

        if ($object->hasData(TriggerDataProvider::CHAIN)) {
            foreach ($object->getData(TriggerDataProvider::CHAIN) as $chainId => $chainData) {
                $chain = $this->chainRepository->get($chainId);
                $chain->addData($chainData)
                    ->setTriggerId($object->getId());

                $this->chainRepository->save($chain);
            }
        }

        return $this;
    }
}