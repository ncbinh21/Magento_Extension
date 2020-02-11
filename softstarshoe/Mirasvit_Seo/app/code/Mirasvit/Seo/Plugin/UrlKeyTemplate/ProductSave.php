<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Plugin\UrlKeyTemplate;

use Magento\Framework\Registry;

class ProductSave
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Mirasvit\Seo\Api\Config\ProductUrlTemplateConfigInterface
     */
    protected $productUrlTemplateConfig;

    /**
     * @var \Mirasvit\Seo\Model\SeoObject\ProducturlFactory
     */
    protected $objectProducturlFactory;

    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Builder
     */
    protected $productBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\App\RequestInterface                     $request
     * @param \Mirasvit\Seo\Api\Config\ProductUrlTemplateConfigInterface  $productUrlTemplateConfig
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Mirasvit\Seo\Api\Config\ProductUrlTemplateConfigInterface $productUrlTemplateConfig,
        \Mirasvit\Seo\Model\SeoObject\ProducturlFactory $objectProducturlFactory,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        Registry $registry
    ) {
         $this->request = $request;
         $this->productUrlTemplateConfig = $productUrlTemplateConfig;
         $this->objectProducturlFactory = $objectProducturlFactory;
         $this->productBuilder = $productBuilder;
         $this->registry = $registry;

    }

    /**
     * Set url_key from Product URL Key Template (if url_key is empty)
     *
     * @param Magento\Catalog\Controller\Adminhtml\Product\Save\Interceptor $subject
     *
     * @return void
     */
    public function beforeExecute($subject)
    {
        $postValue = $this->request->getPostValue();
        if(!isset($postValue['product']) || !isset($postValue['product']['current_store_id']) ) {
            return false;
        }
        $storeId = $postValue['product']['current_store_id'];
        $urlKey = trim($postValue['product']['url_key']);
        $currentProductId = trim($postValue['product']['current_product_id']);
        $urlKeyTemplate = $this->productUrlTemplateConfig-> getProductUrlKey($storeId);
        if (!$urlKey && $urlKeyTemplate && strpos($urlKeyTemplate, '[') !== false) {
            $product = $this->productBuilder->build($this->request);
            $templ = $this->objectProducturlFactory->create()
                            ->setProduct($product);
            $urlKey = $templ->parse($urlKeyTemplate);
            $urlKey = $product->formatUrlKey($urlKey);
            $postValue['product']['url_key'] = $urlKey;
            $this->request->setPostValue('product', $postValue['product']);

            //unregister from product builder
            $this->registry->unregister('product');
            $this->registry->unregister('current_product');
            $this->registry->unregister('current_store');
        }
    }

}