<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 22/06/2018
 * Time: 01:08
 */

namespace Forix\ProductWizard\Model;

use Forix\ProductWizard\Api;
use Forix\ProductWizard\Model\ResourceModel\Product\Link as RelationLink;
use \Magento\Framework\Api\SortOrder;
use \Magento\Framework\Api\SearchResultsInterfaceFactory;

class RelationCollectionProvider implements Api\RelationCollectionProviderInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $_collectionProcessor;

    /**
     * @var \Magento\Catalog\Model\ProductRenderFactory
     */
    protected $_productRenderFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_productCollectionProvider;

    /**
     * RelationCollectionProvider constructor.
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ProductCollectionProvider $productCollectionProvider
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProductCollectionProvider $productCollectionProvider,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    )
    {
        $this->_productRepository = $productRepository;
        $this->_storeManager = $storeManager;
        $this->_productCollectionProvider = $productCollectionProvider;
        $this->_collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
    }

    /**
     * @param string $sku
     * @param integer $storeId
     * @return \Magento\Catalog\Model\ProductRender[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLinkedProducts($sku, $storeId)
    {
        $product = $this->_productRepository->get($sku);
        return [$this->_productCollectionProvider->buildRenderer($product, $storeId)];
    }

    /**
     * Retrieve BillingCode matching the specified criteria.
     * @param integer $storeId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param string $sku
     * @return \Magento\Catalog\Model\ProductRender[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList($storeId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $sku)
    {
        /**
         * @var $product \Forix\ProductWizard\Model\Product
         */
        $product = $this->_productRepository->get($sku);
        $collection = $product->getRelationProductCollection();

        $collection->addAttributeToSelect('name');
        $collection->addStoreFilter($storeId, false);
        $this->_collectionProcessor->process($searchCriteria, $collection);
        return $this->_productCollectionProvider->buildResponse($collection, $storeId);
    }


    /**
     * Retrieve collection processor
     *
     * @deprecated 101.1.0
     * @return \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private function getCollectionProcessor()
    {
        if (!$this->_collectionProcessor) {
            $this->_collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Magento\Catalog\Model\Api\SearchCriteria\ProductCollectionProcessor'
            );
        }
        return $this->_collectionProcessor;
    }
}