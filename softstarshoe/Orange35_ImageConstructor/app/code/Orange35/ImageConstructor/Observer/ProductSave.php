<?php
namespace Orange35\ImageConstructor\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use \Orange35\ImageConstructor\Model\Uploader;

class ProductSave implements ObserverInterface
{
    /**
     * @var Uploader
     */
    protected $uploader;

    public function __construct(
        Uploader $uploader
    )
    {
        $this->uploader = $uploader;
    }

    public function execute(Observer $observer)
    {
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getProduct();
        $productOptions = $product->getOptions() ?: [];
        $newOptions = [];
        /** @var \Magento\Catalog\Model\Product\Option $option */
        foreach ($productOptions as $option) {
            $values = $option->getData('values') ?: [];
            $newValues = [];
            foreach ($values as $value) {
                $newValue = [];
                if(isset($value['layer'])){
                    $name = $this->uploader->uploadFileAndGetName('layer', $value);
                    $newValue['layer'] = $name;
                }
                $newValues[] = array_replace_recursive($value, $newValue);
            }
            $option->setData('values', $newValues);
            $newOptions[] = $option;
        }
        $product->setOptions($newOptions);
        return $this;
    }
}