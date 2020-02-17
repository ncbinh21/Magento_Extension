<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Model\ResourceModel\OptionSetting;

use Magento\Framework\DB\Select;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var Option\CollectionFactory
     */
    private $optionCollectionFactory;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Option\CollectionFactory $optionCollectionFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->optionCollectionFactory = $optionCollectionFactory;
    }

    /**
     * Collection protected constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Amasty\ShopbyBase\Model\OptionSetting::class,
            \Amasty\ShopbyBase\Model\ResourceModel\OptionSetting::class
        );
        $this->_idFieldName = $this->getResource()->getIdFieldName();
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @return $this
     * @throws \Exception
     */
    public function addItem(\Magento\Framework\DataObject $item)
    {
        if (isset($item['title']) && isset($item['value']) && isset($item['store_id'])) {
            $title = $item['title'];
            if (!$title) {
                $title = $this->getValueFromMagentoEav($item['value'], $item['store_id']);
                $item['title'] = $title;
            }
        }

        if (isset($item['meta_title']) && isset($item['value']) && isset($item['store_id'])) {
            $metaTitle = $item['meta_title'];
            if (!$metaTitle) {
                if (isset($title)) {
                    $metaTitle = $title;
                } else {
                    $metaTitle = $this->getValueFromMagentoEav($item['value'], $item['store_id']);
                }

                $item['meta_title'] = $metaTitle;
            }
        }

        return parent::addItem($item);
    }

    /**
     * @param string $filterCode
     * @param int $optionId
     * @param int $storeId
     * @return $this
     */
    public function addLoadParams($filterCode, $optionId, $storeId)
    {
        $listStores = [0];
        if ($storeId > 0) {
            $listStores[] = $storeId;
        }
        $this->addFieldToFilter('filter_code', $filterCode)
            ->addFieldToFilter('value', $optionId)
            ->addFieldToFilter('store_id', $listStores)
            ->addOrder('store_id', self::SORT_ORDER_DESC);
        return $this;
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getHardcodedAliases($storeId)
    {
        $select = $this->getSelect();
        $select->reset(Select::COLUMNS);
        $select->columns('filter_code');
        $select->columns('value');
        if ($storeId === 0) {
            $select->columns('url_alias');
            $select->where('`url_alias` <> ""');
            $select->where('`store_id` = ' . $storeId);
        } else {
            $urlAlias = 'IFNULL(`current_table`.`url_alias`, `main_table`.`url_alias`)';
            $select->joinLeft(
                ['current_table' => $this->getMainTable()],
                '`current_table`.`value` = `main_table`.`value`'
                . " AND `current_table`.`store_id` = $storeId"
                . ' AND `current_table`.`url_alias` <> ""',
                ['url_alias' => $urlAlias]
            );
            $select->where('`main_table`.`store_id` = ?', 0);
            $select->where("$urlAlias  <> ?", '');
        }

        $data = $select->getConnection()->fetchAll($select);
        return $data;
    }

    /**
     * @param $value
     * @param $storeId
     * @return mixed
     */
    public function getValueFromMagentoEav($value, $storeId)
    {
        $optionValue = $this->optionCollectionFactory->create()
            ->join(
                ['option' => 'eav_attribute_option_value'],
                'option.option_id = main_table.option_id'
            )
            ->addFieldToFilter('main_table.option_id', $value)
            ->addFieldToFilter('option.store_id', $storeId ?: 0)
            ->getFirstItem()
            ->getValue();

        return $optionValue;
    }
}
