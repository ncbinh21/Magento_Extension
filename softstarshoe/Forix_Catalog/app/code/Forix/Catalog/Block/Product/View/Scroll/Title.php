<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2 - SoftStart Shoes
 */
namespace Forix\Catalog\Block\Product\View\Scroll;

class Title extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Product
     */
    protected $product = null;

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
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
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
}