<?php
namespace Orange35\Colorpickercustom\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Orange35\Colorpickercustom\Model\Uploader;

class ProductSave implements ObserverInterface
{
    /**
     * @var Uploader
     */
    protected $uploader;

    /**
     * ProductSave constructor.
     * @param Uploader $uploader
     */
    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
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
                $newValue = ['image' => null];
                if (isset($value['image'])) {
                    $name = $this->uploader->uploadFileAndGetName('image', $value);
                    $newValue['image'] = $name;
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