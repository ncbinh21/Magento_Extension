<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 18/10/2016
 * Time: 16:29 CH
 */

namespace Yosto\InstagramConnect\Controller\Instagram;

use Magento\Framework\App\Action\Context;

class Display extends \Magento\Framework\App\Action\Action
{
    /** @var  \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $productRepository;

    /**
     * Display constructor.
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context
    )
    {
        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('id');
        if ($productId != null) {
            $productRepository = $this->_objectManager->get('Magento\Catalog\Model\ProductRepository');
            $product = $productRepository->getById($productId);
            $list = [
                'sku' => $product->getSku(),
                'name' => $product->getName(),
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
}