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
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;
use Mirasvit\Seo\Api\Repository\CanonicalRewriteRepositoryInterface;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterfaceFactory;
use Mirasvit\Seo\Model\ResourceModel\CanonicalRewrite\CollectionFactory;
use Mirasvit\Seo\Api\Data\CanonicalRewriteStoreInterface;

class CanonicalRewriteRepository implements CanonicalRewriteRepositoryInterface
{
    /**
     * @var DomainInterfaceFactory
     */
    private $factory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        CanonicalRewriteInterfaceFactory $factory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->factory = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->factory->create();
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
    public function get($id)
    {
        $model = $this->create();

        $this->entityManager->load($model, $id);

        return $model->getId() ? $model : false;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CanonicalRewriteInterface $model)
    {
        $model = $this->prepareRuleData($model);

        $result = $this->entityManager->save($model);

        $this->saveStore($model);


        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CanonicalRewriteInterface $model)
    {
        return $this->entityManager->delete($model);
    }

    /**
     * @param CanonicalRewriteInterface $model
     *
     * @return CanonicalRewriteInterface
     */
    protected function prepareRuleData($model)
    {
        // Serialize conditions
        if ($model->getConditions()
            && ($ruleData = $model->getData('rule'))) {
            $model->loadPost($ruleData);
            $serializer = $this->getSerializer();
            if ($serializer) {
                $conditions = $serializer->serialize($model->getConditions()->asArray());
            } else {
                $conditions = serialize($model->getConditions()->asArray());
            }
            $model->setConditionsSerialized($conditions);
        }

        // Serialize actions
        if ($model->getActions()) {
            $serializer = $this->getSerializer();
            if ($serializer) {
                $actions = $serializer->serialize($model->getActions()->asArray());
            } else {
                $actions = serialize($model->getActions()->asArray());
            }
            $model->setActionsSerialized($actions);
        }

        return $model;
    }

    /**
     * @return \Magento\Framework\Serialize\Serializer\Json|false
     */
    protected function getSerializer()
    {
        $serializer = false;
        if (class_exists(\Magento\Framework\Serialize\Serializer\Json::class)) {
            $serializer = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Serialize\Serializer\Json::class
            );
        }

        return $serializer;

    }

    /**
     * @param CanonicalRewriteInterface $model
     *
     * @return void
     */
    protected function saveStore($model)
    {
        if ($model->getData('store_ids')) {
            $connection = $this->resource->getConnection();
            $condition = $connection->quoteInto(
                CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID . ' = ?', $model->getId()
            );
            $connection->delete($this->resource->getTableName(CanonicalRewriteStoreInterface::TABLE_NAME), $condition);
            foreach ((array)$model->getData('store_ids') as $store) {
                $storeArray = [
                    CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID => $model->getId(),
                    CanonicalRewriteStoreInterface::STORE_ID => $store,
                ];
                $connection->insert(
                    $this->resource->getTableName(CanonicalRewriteStoreInterface::TABLE_NAME),
                    $storeArray
                );
            }
        }
    }
}