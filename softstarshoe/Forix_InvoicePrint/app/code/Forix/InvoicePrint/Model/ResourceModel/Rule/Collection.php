<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 27
 * Time: 15:29
 */

namespace Forix\InvoicePrint\Model\ResourceModel\Rule;

use Forix\InvoicePrint\Model\Rule;
use Forix\InvoicePrint\Model\ResourceModel\Rule as ResourceModelRule;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Collection
 *
 * @package Forix\InvoicePrint\Model\ResourceModel\Rule
 */
class Collection extends AbstractCollection
{
    /**
     * Primary column
     *
     * @var string
     */
    protected $_idFieldName = 'rule_id';

    /**
     * @var DateTime
     */
    protected $_coreDate;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param DateTime $dateTime
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        DateTime $dateTime,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ){
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_coreDate = $dateTime;
    }

    /**
     *  Initialize resource collection
     *
     *  @return void
     */
    public function _construct()
    {
        $this->_init(Rule::class, ResourceModelRule::class);
    }

    /**
     * @return $this
     */
    public function getAvailableCollection()
    {

        $this->addActiveFilter()
            ->setOrder('priority', 'ASC');

        return $this;
    }

    /**
     * Add enable filter to collection
     *
     * @return $this
     */
    public function addActiveFilter()
    {
        return $this->addFieldToFilter('status', 1);
    }

    /**
     * Add store availability filter. Include availability product
     * for store website
     *
     * @param null|string|bool|int $store
     * @return $this
     */
    public function addStoreToFilter($store = null)
    {
        $conditions = [
            'label_store.rule_id = main_table.rule_id',
            'label_store.store_id = '.$store
        ];
        $this->getSelect()->join(
            ['label_store' => $this->getTable(ResourceModelRule::INVOICE_PRINT_STORE_TABLE)],
            join(' AND ', $conditions),
            []
        );

        return $this;
    }


    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'rule_id', $labelField = 'name', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
