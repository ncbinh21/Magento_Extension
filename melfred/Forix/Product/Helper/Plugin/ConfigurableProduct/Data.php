<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 31/07/2018
 * Time: 16:46
 */

namespace Forix\Product\Helper\Plugin\ConfigurableProduct;

use \Forix\Product\Helper\Data as HelperData;

class Data extends \Magento\ConfigurableProduct\Helper\Data
{
    protected $_stockRegistry;
    protected $_helper;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    public function __construct(
        \Magento\Catalog\Helper\Image $imageHelper,
        HelperData $helper,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_helper = $helper;
        $this->_registry = $registry;
        parent::__construct($imageHelper);

    }

    public function afterGetOptions($subject, $options, $currentProduct, $allowedProducts)
    {
        $aStockStatus = [
            'messages' => [],
            'rigModelOptions' => [],
            'rigModelId' => 0
        ];
        $allowAttributes = $this->getAllowAttributes($currentProduct);
        $aStockStatus['rigModelId'] = $this->_helper->getAttributeIdByCode('mb_rig_model');
        foreach ($allowedProducts as $product) {
            $key = [];
            foreach ($allowAttributes as $attribute) {
                $key[] = $product->getData($attribute->getData('product_attribute')->getData('attribute_code'));
            }
            if ($key) {
                $aStockStatus['messages'][implode(',', $key)]['stock_message'] = $this->_helper->getStockMessage($product);
            }
        }
        $this->_registry->register('forix_stock_message_data', $aStockStatus, true);
        return $options;
    }

}