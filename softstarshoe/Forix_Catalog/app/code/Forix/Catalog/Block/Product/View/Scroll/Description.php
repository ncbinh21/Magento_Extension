<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2 - SoftStart Shoes
 */

namespace Forix\Catalog\Block\Product\View\Scroll;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Forix\Catalog\Observer\AddAdditionalOption;

class Description extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Product
     */
    protected $product = null;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * Title constructor.
     * @param \Magento\Framework\View\Element\Template $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->product) {
            $this->product = $this->registry->registry('product');
        }
        return $this->product;
    }

    /**
     * @param $product
     * @return bool
     */
    public function isCustomOption($product)
    {
        if($options = $product->getOptions()){
            foreach ($options as $option) {
                if($option->getIsColorpicker()){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $product
     * @return array
     */
    public function getAttributeClearance($product)
    {
        $additionalOptions = [];
        $isClearance = $this->isClearance($product);
        if($product->getTypeId() == 'simple' && $isClearance) {
            $listAttributes = $this->_scopeConfig->getValue(AddAdditionalOption::LIST_ATTRIBUTE_CLEARANCE);
            $arrListAttributes = explode("\n", $listAttributes);

            foreach ($arrListAttributes as $attribute) {
                $attribute = trim(strtr($attribute , array("\r" => "")));
                if($product->getResource()->getAttribute($attribute)) {
                    $optionTitle = $product->getResource()->getAttribute($attribute)->getFrontendLabel();
                    if(!($optionText = $product->getResource()->getAttribute($attribute)->getSource()->getOptionText($product->getData($attribute)))) {
                        $optionText = $product->getData($attribute);
                    }
                    if($optionTitle && $optionText){
                        $additionalOptions[] = array(
                            'label' => $optionTitle,
                            'value' => $optionText,
                        );
                    }
                }
            }
        }
        return $additionalOptions;
    }

    /**
     * @param $product
     * @return bool
     */
    public function isClearance($product)
    {
        $categoryIds = $product->getCategoryIds();
        if(isset($categoryIds) && is_array($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                $category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
                if($category->getName() == 'Clearance' || $category->getParentCategory()->getName() == 'Clearance') {
                    return true;
                }
            }
        }
        return false;
    }
}