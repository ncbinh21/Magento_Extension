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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Service\Alternate;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewrite;

class ProductStrategy implements \Mirasvit\Seo\Api\Service\Alternate\StrategyInterface
{
    /**
     * @var \Mirasvit\Seo\Api\Service\Alternate\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url,
        \Magento\Framework\Registry $registry,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->url = $url;
        $this->registry = $registry;
        $this->urlFinder = $urlFinder;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreUrls()
    {
        $storeUrls = $this->url->getStoresCurrentUrl();
        $storeUrls = $this->getAlternateUrl($storeUrls);

        return $storeUrls;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlternateUrl($storeUrls)
    {
        $product = $this->registry->registry('current_product');
        $productId = $product->getId();
        foreach ($this->url->getStores() as $storeId => $store) {
            $idPath = $this->request->getPathInfo();

            if ($idPath && strpos($idPath, $productId)) {
                $rewriteObject = $this->urlFinder->findOneByData([
                    UrlRewrite::TARGET_PATH => trim($idPath, '/'),
                    UrlRewrite::STORE_ID => $storeId,
                ]);
                if ($rewriteObject && ($requestPath = $rewriteObject->getRequestPath())) {
                    $storeUrls[$storeId] = $store->getBaseUrl().$requestPath.$this->url->getUrlAddition($store);
                }
            }
        }

        return $storeUrls;
    }


}