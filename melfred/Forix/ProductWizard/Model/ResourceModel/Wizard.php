<?php


namespace Forix\ProductWizard\Model\ResourceModel;

use \Magento\Framework\Model\AbstractModel;
use Magento\Cms\Model\Page as CmsPage;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\EntityManager\EntityManager;
use \Forix\ProductWizard\Api\Data\WizardInterface;
use \Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use \Forix\ProductWizard\Model\Source\TemplateUpdate;

class Wizard extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{


    /**
     * @var \Forix\ProductWizard\Model\WizardUrlPathGenerator
     */
    protected $wizardUrlPathGenerator;

    /**
     * @var \Magento\UrlRewrite\Model\UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * Store model
     *
     * @var null|Store
     */
    protected $_store = null;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    protected $_groupCollectionFactory;

    protected $_categoryConfig;
    /**
     * @var Product\CollectionFactory
     */
    protected $_productCollectionFactory;


    protected $_productCollectionProvider;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param DateTime $dateTime
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        \Magento\Catalog\Model\Config $categoryConfig,
        \Forix\ProductWizard\Model\WizardUrlPathGenerator $wizardUrlPathGenerator,
        \Forix\ProductWizard\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \Forix\ProductWizard\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Forix\ProductWizard\Model\ProductCollectionProvider $productCollectionProvider,
        \Magento\UrlRewrite\Model\UrlPersistInterface $urlPersist,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->wizardUrlPathGenerator = $wizardUrlPathGenerator;
        $this->urlPersist = $urlPersist;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->_productCollectionProvider = $productCollectionProvider;
        $this->_groupCollectionFactory = $groupCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryConfig = $categoryConfig;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_productwizard_wizard', 'wizard_id');
    }


    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(WizardInterface::class)->getEntityConnection();
    }

    /**
     * Process page data before saving
     *
     * @param AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $urlKey = $object->getData('identifier');
        if ($urlKey === '' || $urlKey === null) {
            $object->setData('identifier', $this->wizardUrlPathGenerator->generateUrlKey($object));
        }

        if (!$this->isValidPageIdentifier($object)) {
            throw new LocalizedException(
                __('The page URL key contains capital letters or disallowed symbols.')
            );
        }

        if ($this->isNumericPageIdentifier($object)) {
            throw new LocalizedException(
                __('The page URL key cannot be made of only numbers.')
            );
        }
        $object->setData('step_count', TemplateUpdate::getStepCount($object->getData('template_update')));
        return parent::_beforeSave($object);
    }


    /**
     * Set store model
     *
     * @param Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->_store);
    }

    /**
     * @param $skus
     * @return \Magento\Catalog\Model\ProductRender[]
     */
    public function getAvailableProductCollection($skus)
    {
        if (!empty($skus)) {
            $collection = $this->_productCollectionFactory->create()->addAttributeToFilter('sku', ['in' => $skus]);
            $collection->addAttributeToSelect($this->_categoryConfig->getProductAttributes());
            $products = $this->_productCollectionProvider->buildResponse($collection, $this->_storeManager->getStore()->getId());
            return $products;
        }
        return null;
    }

    /**
     * Return Array of SKUs include group item option
     * @param \Magento\Framework\Model\AbstractModel $wizard
     * @return array
     */
    public function getAvailableProductSku($wizard)
    {
        $select = $this->getConnection()->select()->from($this->getConnection()->getTableName('forix_productwizard_group_item_option'), ['product_sku'])
            ->where('wizard_id = ? and product_sku is not null', $wizard->getId());
        $result = $this->getConnection()->fetchCol($select);
        return $result;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $pageId
     * @return array
     */
    public function lookupStoreIds($pageId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(WizardInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['cps' => $this->getTable('forix_wizard_store')], 'store_id')
            ->join(
                ['cp' => $this->getMainTable()],
                'cps.' . $linkField . ' = cp.' . $linkField,
                []
            )
            ->where('cp.' . $entityMetadata->getIdentifierField() . ' = :wizard_id');

        return $connection->fetchCol($select, ['wizard_id' => (int)$pageId]);
    }

    /**
     * @param AbstractModel $object
     * @param string $value
     * @param string|null $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws \Exception
     */
    private function getWizardId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(WizardInterface::class);

        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }

        $pageId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $pageId = count($result) ? $result[0] : false;
        }
        return $pageId;
    }

    /**
     * @param \Forix\ProductWizard\Model\Wizard $object
     * @param mixed $value
     * @param null $field
     * @return $this|AbstractDb
     * @throws LocalizedException
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $pageId = $this->getWizardId($object, $value, $field);
        if ($pageId) {
            $this->entityManager->load($object, $pageId);
        }
        return $this;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param CmsPage|AbstractModel $object
     * @return Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $entityMetadata = $this->metadataPool->getMetadata(WizardInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [
                Store::DEFAULT_STORE_ID,
                (int)$object->getStoreId(),
            ];
            $select->join(
                ['forix_wizard_store' => $this->getTable('forix_wizard_store')],
                $this->getMainTable() . '.' . $linkField . ' = forix_wizard_store.' . $linkField,
                []
            )
                ->where('is_active = ?', 1)
                ->where('forix_wizard_store.store_id IN (?)', $storeIds)
                ->order('forix_wizard_store.store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(WizardInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $this->getConnection()->select()
            ->from(['cp' => $this->getMainTable()])
            ->join(
                ['cps' => $this->getTable('forix_wizard_store')],
                'cp.' . $linkField . ' = cps.' . $linkField,
                []
            )
            ->where('cp.identifier = ?', $identifier)
            ->where('cps.store_id IN (?)', $store);

        if ($isActive !== null) {
            $select->where('cp.is_active = ?', $isActive);
        }

        return $select;
    }

    /**
     *  Check whether page identifier is numeric
     *
     * @param AbstractModel $object
     * @return bool
     */
    protected function isNumericPageIdentifier(AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether page identifier is valid
     *
     * @param AbstractModel $object
     * @return bool
     */
    protected function isValidPageIdentifier(AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }

    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->isDeleted()) {
            $this->urlPersist->deleteByData(
                [
                    UrlRewrite::ENTITY_ID => $object->getId(),
                    UrlRewrite::ENTITY_TYPE => \Forix\ProductWizard\Model\WizardUrlRewriteGenerator::ENTITY_TYPE
                ]
            );
        }
        return parent::_afterDelete($object); // TODO: Change the autogenerated stub
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $entityMetadata = $this->metadataPool->getMetadata(WizardInterface::class);

        $stores = [Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(Select::COLUMNS)
            ->columns('cp.' . $entityMetadata->getIdentifierField())
            ->order('cps.store_id DESC')
            ->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }


    public function getGroups(AbstractModel $object)
    {
        /**
         * @var $collection \Forix\ProductWizard\Model\ResourceModel\Group\Collection
         */
        $collection = $this->_groupCollectionFactory->create();
        $collection->addWizardToFilter($object->getId());
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }
}
