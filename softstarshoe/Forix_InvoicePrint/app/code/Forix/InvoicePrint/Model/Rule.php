<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 27
 * Time: 15:29
 */

namespace Forix\InvoicePrint\Model;

use Magento\Rule\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Forix\InvoicePrint\Model\Rule\Condition\CombineFactory;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Rule\Model\Action\CollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\CatalogRule\Model\Rule as CatalogRule;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Rule
 *
 * @package Forix\InvoicePrint\Model
 * @method array getStoreIds()
 * @method array getStoreId()
 * @method int getType()
 */
class Rule extends AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'forix_invoiceprint_print';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getLabel() in this case
     *
     * @var string
     */
    protected $_eventObject = 'invoice_print_rule';

    /**
     * @var \Magento\CatalogRule\Model\Rule\Condition\CombineFactory
     */
    protected $_combineFactory;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Action\CollectionFactory
     */
    protected $_actionCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * Rule constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param CombineFactory $combineFactory
     * @param CollectionFactory $actionCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param ImageUploader $imageUploader
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $relatedCacheTypes
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CombineFactory $combineFactory,
        CollectionFactory $actionCollectionFactory,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $relatedCacheTypes = [],
        array $data = []
    ) {
        $this->_combineFactory = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;
        $this->_storeManager = $storeManager;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Init resource model and id field
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Rule::class);
        $this->setIdFieldName('rule_id');
    }

    /**
     * Getter for rule conditions collection
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    /**
     * Getter for rule actions collection
     *
     * @return \Magento\CatalogRule\Model\Rule\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getActionsFieldSetId($formName = '')
    {
        $this->getActions();
        return $formName . 'rule_actions_fieldset_' . $this->getId();
    }


    /**
     * @return mixed
     */
    public function getConditionsUnSerialized()
    {
        return unserialize($this->getConditionsSerialized());
    }
}
