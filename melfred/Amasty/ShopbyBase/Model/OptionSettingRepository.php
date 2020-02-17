<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Model;

use Amasty\ShopbyBase\Api\Data\OptionSettingRepositoryInterface;
use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Amasty\ShopbyBase\Model\ResourceModel\OptionSetting as OptionSettingResource;
use Magento\Framework\Exception\NoSuchEntityException;

class OptionSettingRepository implements OptionSettingRepositoryInterface
{
    /**
     * @var OptionSettingResource
     */
    private $resource;

    /**
     * @var OptionSettingFactory
     */
    private $factory;

    /**
     * @var OptionSettingResource\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        OptionSettingResource $resource,
        OptionSettingFactory $factory,
        ResourceModel\OptionSetting\CollectionFactory $collectionFactory
    ) {
        $this->resource = $resource;
        $this->factory = $factory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param int $id
     * @return OptionSettingInterface
     * @throws NoSuchEntityException
     */
    public function get($id)
    {
        $entity = $this->factory->create();
        $this->resource->load($entity, $id);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(__('Requested option setting doesn\'t exist'));
        }
        return $entity;
    }

    /**
     * @param string $filterCode
     * @param int $optionId
     * @param int $storeId
     * @return OptionSettingInterface
     */
    public function getByParams($filterCode, $optionId, $storeId)
    {
        $collection = $this->collectionFactory->create()->addLoadParams($filterCode, $optionId, $storeId);

        /** @var OptionSettingInterface $model */
        $model = $collection->getFirstItem();
        if ($storeId !== 0) {
            $defaultModel = $collection->getLastItem();
            foreach ($model->getData() as $key => $value) {
                if ($defaultModel->getData($key) == $value) {
                    $model->setData($key . '_use_default', true);
                }
            }
        } else {
            foreach (['meta_title', 'title'] as $key) {
                $model->setData($key . '_use_default', false);

                $value = $collection->getValueFromMagentoEav($optionId, $storeId);
                if ($model->getData($key) == $value) {
                    $model->setData($key . '_use_default', true);
                }
            }
        }

        return $model;
    }

    /**
     * @param OptionSettingInterface $optionSetting
     * @return $this
     */
    public function save(OptionSettingInterface $optionSetting)
    {
        $this->resource->save($optionSetting);
        return $this;
    }
}
