<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 27
 * Time: 15:29
 */

namespace Forix\InvoicePrint\Model\Rule;

use Forix\InvoicePrint\Model\ResourceModel\Rule\CollectionFactory;
use Forix\InvoicePrint\Model\ResourceModel\Rule\Collection;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Registry;
use Forix\InvoicePrint\Model\Rule;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\SalesRule\Model\Rule\Metadata\ValueProvider
     */
    protected $metadataValueProvider;

    /**
     * Initialize dependencies.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->coreRegistry = $registry;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        /** @var \Forix\InvoicePrint\Model\Rule $rule */
        $rule = $this->coreRegistry->registry('current_invoiceprint_rule');
        if ($rule->getId()) {
            $ruleData = $rule->getData();
            $this->loadedData[$rule->getId()] = $ruleData;
        } else {
            $this->loadedData = [];
        }

        return $this->loadedData;
    }
}
