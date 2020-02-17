<?php

namespace Forix\Mirasvit\Observer;

class Canonical extends \Mirasvit\Seo\Observer\Canonical
{

	protected $_Data;

	public function __construct(
		\Mirasvit\Seo\Model\Config                                                          $config,
		\Magento\Bundle\Model\Product\TypeFactory                                           $productTypeFactory,
		\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory                     $categoryCollectionFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory                      $productCollectionFactory,
		\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable          $productTypeConfigurable,
		\Magento\Bundle\Model\Product\Type                                                  $productTypeBundle,
		\Magento\GroupedProduct\Model\Product\Type\Grouped                                  $productTypeGrouped,
		\Magento\Framework\View\Element\Template\Context                                    $context,
		\Magento\Framework\Registry                                                         $registry,
		\Mirasvit\Seo\Helper\Data                                                           $seoData,
		\Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection                        $urlRewrite,
		\Mirasvit\Seo\Helper\UrlPrepare                                                     $urlPrepare,
		\Mirasvit\Seo\Api\Service\CanonicalRewrite\CanonicalRewriteServiceInterface         $canonicalRewriteService,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Mirasvit\Seo\Api\Service\PageDetectorInterface $pageDetector,
		\Forix\Base\Helper\Data $data
	) {
		parent::__construct($config,$productTypeFactory,$categoryCollectionFactory,$productCollectionFactory,$productTypeConfigurable,$productTypeBundle,$productTypeGrouped,$context,$registry,$seoData,$urlRewrite,$urlPrepare,$canonicalRewriteService,$productRepository,$pageDetector);
		$this->_Data = $data;
	}

	public function addLinkCanonical($canonicalUrl)
	{
		if ($this->_Data->getFullActionName()!="wordpress_post_view") {
            if(!strpos($canonicalUrl, 'ground-condition') !== false){
                $pageConfig = $this->context->getPageConfig();
                $type = 'canonical';
                $pageConfig->addRemotePageAsset(
                    html_entity_decode($canonicalUrl),
                    $type,
                    ['attributes' => ['rel' => $type]]
                );
            }
		}

	}

}