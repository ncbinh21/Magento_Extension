<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 18/10/2016
 * Time: 16:29 CH
 */

namespace Forix\Custom\Rewrite\Controller\Instagram;

use Magento\Framework\App\Action\Context;

class DisplayCustom extends \Magento\Framework\App\Action\Action
{
    /** @var  \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $dataHelper;

    /**
     * Display constructor.
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Pricing\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {
        $regularPrice = '';
        $productId = $this->getRequest()->getParam('id');
        if ($productId != null) {
            $productRepository = $this->_objectManager->get('Magento\Catalog\Model\ProductRepository');
            $product = $productRepository->getById($productId);
            $isFrom = $this->isCustomOption($product);
            if($product->getPriceInfo()->getPrice('regular_price') && $product->getPriceInfo()->getPrice('final_price')) {
                if($product->getPriceInfo()->getPrice('regular_price')->getValue() != $product->getPriceInfo()->getPrice('final_price')->getValue()) {
                    $regularPrice = $this->dataHelper->currency($product->getPriceInfo()->getPrice('regular_price')->getValue());
                }
            }
            $list = [
                'isFrom' => $isFrom,
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'formatedPriceRegular' => $regularPrice,
                'formatedPrice' => $product->getFormatedPrice(),
                'shortDescription' => $product->getShortDescription(),
                'isInStock' => $product->getIsSalable() ? 'In stock' : 'Out of stock',
                'urlInStore' => $product->getUrlInStore()
            ];

            $this->getResponse()->setBody(json_encode($list));
        } else {
            $this->getResponse()->setBody('');
        }
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