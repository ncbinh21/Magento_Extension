<?php

namespace Forix\Configurable\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;

class Data extends AbstractHelper
{


    private $eavFactory;

    protected $_fitmentAttrInfo;
    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory
     */
    protected $productTypeConfigurable;
    protected $productTypeConfigurableFactory;

    protected $_eavConfig;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    public function __construct(
        Context $context,
        CollectionFactory $eavFactory,
        \Magento\Eav\Model\Config $eavConfig,
        ProductRepositoryInterface $productRepository,
        ConfigurableFactory $productTypeConfigurableFactory
    )
    {
        parent::__construct($context);
        $this->eavFactory = $eavFactory;
        $this->_eavConfig = $eavConfig;
        $this->productRepository = $productRepository;
        $this->productTypeConfigurableFactory = $productTypeConfigurableFactory;
    }

    /**
     * Edited by Hidro Le
     * @return array
     */
    public function getFitMenAttribute()
    {
        if (!$this->_fitmentAttrInfo) {
            $collection = $this->eavFactory->create();
            $collection->addFieldToFilter('is_fitment', 1);
            $attrInfo = [];
            foreach ($collection as $_item) {
                $attrInfo[$_item->getData('attribute_code')] = $_item->getData('frontend_label');
            }
            $this->_fitmentAttrInfo = $attrInfo;
        }
        return $this->_fitmentAttrInfo;
    }

    public function getIdAttribute($code)
    {
        try {
            $attribute = $this->_eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $code);
            return $attribute->getId();
        } catch (\Exception $e) {
        }
        return null;
    }

    public function checkIsRigModels($id)
    {
        $childProductIds = $this->productTypeConfigurableFactory->create()->getChildrenIds($id);
        $flag = false;
        if (isset($childProductIds[0])) {
            foreach ($childProductIds[0] as $childProductId) {
                $childProduct = $this->productRepository->getById($childProductId);
                $text = $childProduct->getAttributeText('mb_rig_model');
                if ($text != "") {
                    $flag = true;
                    break;
                }
            }
        }
        return $flag;
    }

}