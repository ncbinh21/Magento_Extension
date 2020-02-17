<?php
/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 9/12/17
 * Time: 4:11 PM
 */

namespace Forix\Product\Helper\Product;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Matrix extends AbstractHelper
{

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory
     */
    protected $configurableFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\OptionFactory
     */
    protected $_productOptionFactory;
    
    public function __construct(
        Context $context,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $configurableFactory,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->configurableFactory = $configurableFactory;
        $this->productRepository = $productRepository;
        $this->_productOptionFactory = $productOptionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductIdHasOptions(){
        return $this->_productOptionFactory->create()->getCollection()->getColumnValues('product_id');
    }

    /**
     * @return int
     */
    public function isPostData(){
        return count($this->_request->getPost());
    }

    /**
     * @param $id
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProductById($id){
        try{
            if($id) {
                return $this->productRepository->getById($id);
            }
        }catch (\Exception $e){}
        return false;
    }
    /**
     * @param $sku
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProduct($sku){
        try{
            if($sku) {
                return $this->productRepository->get($sku);
            }
        }catch (\Exception $e){}
        return false;
    }

    /**
     * @param $mainProductId
     * @return array
     */
    public function getLinkedData($mainProductId){
        /**
         * @var $configurable \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
         */
        $configurable = $this->configurableFactory->create();
        $linkIds = [];
        $select = $configurable->getConnection()->select()->from(
            ['l' => $configurable->getMainTable()]
        )->where(
            'l.parent_id = ?',
            $mainProductId
        );
        foreach ($configurable->getConnection()->fetchAll($select) as $row) {
            $linkIds[] = $row;
        }
        return $linkIds;
    }
    
    public function isProductHasIncluded($product){
        if(!$product instanceof \Magento\Catalog\Model\Product) {
            $product = $this->productRepository->getById($product);
        }
        if(!($product->getData('relation_skus'))) {
            /**
             * @var $configurable \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
             */
            $linkIds = $this->getLinkedData($product->getId());
            $product->setData('relation_skus', $linkIds);
        }
        $relationSkus = $product->getData('recommend_skus');
        foreach ($relationSkus as $relation){
            if(null != $relation['recommend_sku'] && $relation['recommend_sku']){
                return true;
            }
        }
        return false;
    }
    public function getRecommendData($mainProductId){
        if(!$mainProductId instanceof \Magento\Catalog\Model\Product) {
            $product = $this->productRepository->getById($mainProductId);
        }else{
            $product = $mainProductId;
        }
        if(!($product->getData('recommend_skus'))) {
            /**
             * @var $configurable \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
             */
            $linkIds = $this->getLinkedData($product->getId());
            $product->setData('recommend_skus', $linkIds);
        }
        return  $product->getData('recommend_skus');
    }
    
    /**
     * @param $mainProductId
     * @param $childProductId
     * @return string
     */
    public function getRecommendSku($mainProductId, $childProductId){
        if(!$mainProductId instanceof \Magento\Catalog\Model\Product) {
            $product = $this->productRepository->getById($mainProductId);
        }else{
            $product = $mainProductId;
        }
        if(!($product->getData('recommend_skus'))) {
            /**
             * @var $configurable \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
             */
            $linkIds = $this->getLinkedData($product->getId());
            $product->setData('recommend_skus', $linkIds);
        }
        $relationSkus = $product->getData('recommend_skus');
        foreach ($relationSkus as $relation){
            if($childProductId == $relation['product_id']){
                return $relation['recommend_sku'];
            }
        }
        return '';
    }
}