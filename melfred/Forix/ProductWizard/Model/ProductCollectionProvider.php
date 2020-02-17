<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 07/09/2018
 * Time: 19:57
 */

namespace Forix\ProductWizard\Model;


class ProductCollectionProvider
{

    protected $_productRenderCollectorComposite;
    protected $_websiteRepository;
    protected $_productRenderFactory;
    protected $_collectionProcessor;
    protected $_extensionAttributesJoinProcessor;
    protected $_wizardHelper;
    protected $_productTypeConfigurable;
    protected $_productRepository;

    public function __construct(
        \Forix\ProductWizard\Ui\DataProvider\Product\ProductRenderCollectorComposite $productRenderCollectorComposite,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $productTypeConfigurable,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ProductRenderFactory $productRenderFactory,
        \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \Forix\ProductWizard\Helper\Data $wizardHelper
    )
    {
        $this->_productTypeConfigurable = $productTypeConfigurable;
        $this->_productRenderCollectorComposite = $productRenderCollectorComposite;
        $this->_websiteRepository = $websiteRepository;
        $this->_productRenderFactory = $productRenderFactory;
        $this->_extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->_wizardHelper = $wizardHelper;
        $this->_productRepository = $productRepository;
    }


    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function buildCollection($collection, $storeId)
    {
        if ($attributes = $this->_wizardHelper->getConfigAttributes(null, $storeId)) {
            if (count($attributes)) {
                $collection->addAttributeToSelect($attributes, 'left');
            }
        }
        return $collection;
    }

    /**
     * @param $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed|null
     */
    protected function getParentProduct($productId)
    {
        /**
         * Chắc chắn không lặp lại lần thứ 2 do không có product bị trùng trong collection ở buildRenderer
         */
        $ids = $this->_productTypeConfigurable->getParentIdsByChild($productId);
        if ($ids && count($ids)) {
            $configurableId = $ids[0];
            try {
                return $this->_productRepository->getById($configurableId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            }
        }
        return null;
    }

    /**
     * @param \Magento\Catalog\Model\Product $_product
     * @param int $storeId
     * @return \Magento\Catalog\Model\ProductRender
     */
    public function buildRenderer($_product, $storeId)
    {

        $productRenderInfo = $this->_productRenderFactory->create();
        $productRenderInfo->setStoreId($storeId);
        $productRenderInfo->setData('parent', $this->getParentProduct($_product->getEntityId()));
        $productRenderInfo->setCurrencyCode($this->_websiteRepository->getDefault()->getBaseCurrencyCode());
        $this->_productRenderCollectorComposite->collect($_product, $productRenderInfo);
        $productRenderInfo->setData('is_required', $_product->getIsRequired());
        $productRenderInfo->setData('attribute_set_id', $_product->getAttributeSetId());
        $productRenderInfo->setData('product', $_product);
        return $productRenderInfo;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param int $storeId
     * @return \Magento\Catalog\Model\ProductRender[]
     */
    public function buildResponse($collection, $storeId)
    {
        $products = [];
        $this->buildCollection($collection, $storeId);
        foreach ($collection as $_product) {
            $products[$_product->getId()] = $this->buildRenderer($_product, $storeId);
        }
        return $products;
    }
}