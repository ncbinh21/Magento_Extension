<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: scratchwizard.local
 */

namespace Forix\Custom\Rewrite\Mirasvit\SeoSitemap\Model;

class Sitemap extends \Mirasvit\SeoSitemap\Model\Sitemap
{
    protected $eavAttribute;

    public function __construct(
        \Magento\Eav\Api\AttributeRepositoryInterface $eavAttribute,
        \Mirasvit\SeoSitemap\Api\Config\CmsSitemapConfigInterface $cmsSitemapConfig,
        \Mirasvit\SeoSitemap\Api\Config\LinkSitemapConfigInterface $linkSitemapConfig,
        \Mirasvit\SeoSitemap\Helper\Data $seoSitemapData,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Escaper $escaper,
        \Magento\Sitemap\Helper\Data $sitemapData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory $categoryFactory,
        \Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory $productFactory,
        \Magento\Sitemap\Model\ResourceModel\Cms\PageFactory $cmsFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $modelDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->eavAttribute = $eavAttribute;
        parent::__construct($cmsSitemapConfig, $linkSitemapConfig, $seoSitemapData, $context, $registry, $escaper, $sitemapData, $filesystem, $categoryFactory, $productFactory, $cmsFactory, $modelDate, $storeManager, $request, $dateTime, $moduleManager, $objectManager, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize sitemap items
     *
     * @return void
     */
    protected function _initSitemapItems()
    {
        $this->getPreparedSitemapPath();

        /** @var $helper \Magento\Sitemap\Helper\Data */
        $helper = $this->_sitemapData;
        $storeId = $this->getStoreId();

        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'changefreq' => $helper->getCategoryChangefreq($storeId),
                'priority' => $helper->getCategoryPriority($storeId),
                'collection' => $this->_categoryFactory->create()->getCollection($storeId),
            ]
        );

        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'changefreq' => $helper->getProductChangefreq($storeId),
                'priority' => $helper->getProductPriority($storeId),
                'collection' => $this->_productFactory->create()->getCollection($storeId),
            ]
        );

        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'changefreq' => $helper->getPageChangefreq($storeId),
                'priority' => $helper->getPagePriority($storeId),
                'collection' => $this->getCmsPages($storeId),
            ]
        );

        if ($this->moduleManager->isEnabled('Aheadworks_Blog')
            && class_exists('\Aheadworks\Blog\Helper\Sitemap')) {
            $sitemapHelper = $this->objectManager->get('\Aheadworks\Blog\Helper\Sitemap');
            $this->_sitemapItems[] = $sitemapHelper->getBlogItem($storeId);
            $this->_sitemapItems[] = $sitemapHelper->getCategoryItems($storeId);
            $this->_sitemapItems[] = $sitemapHelper->getPostItems($storeId);
        }

        if ($this->moduleManager->isEnabled('Mirasvit_Blog')) {
            $blogMxSitemap = $this->objectManager->get('\Mirasvit\SeoSitemap\Api\Data\BlogMx\SitemapInterface');
            $this->_sitemapItems[] = $blogMxSitemap->getBlogItem();
            if ($categoryItems = $blogMxSitemap->getCategoryItems()) {
                $this->_sitemapItems[] = $categoryItems;
            }
            if ($postItems = $blogMxSitemap->getPostItems($storeId)) {
                $this->_sitemapItems[] = $postItems;
            }
        }

        if ($this->moduleManager->isEnabled('Mirasvit_Kb')
            && interface_exists('\Mirasvit\Kb\Api\Data\SitemapInterface')) {
            $kbSitemap = $this->objectManager->get('\Mirasvit\Kb\Api\Data\SitemapInterface');
            $this->_sitemapItems[] = $kbSitemap->getBlogItem($storeId);
            if ($categoryItems = $kbSitemap->getCategoryItems($storeId)) {
                $this->_sitemapItems[] = $categoryItems;
            }
            if ($postItems = $kbSitemap->getPostItems($storeId)) {
                $this->_sitemapItems[] = $postItems;
            }
        }

        $helperManufacture = $this->objectManager->create('\Forix\Megamenu\Helper\Data');
        $rigModelAttribute = $helperManufacture->getRigModelAttribute();
        $rigModels = $rigModelAttribute->getOptions();

        $blockMegamenu = $this->objectManager->create('\Forix\Megamenu\Rewrite\Ves\Megamenu\Block\Menu');
        $urlCustom = [];
        array_push($urlCustom, 'blog');
        $manufacturerAttr = $this->eavAttribute->get(\Magento\Catalog\Model\Product::ENTITY, 'mb_ground_condition');
        $allOptions = $manufacturerAttr->getSource()->getAllOptions();
        foreach ($allOptions as $option) {
            if(isset($option['label']) && trim($option['label'])) {
                array_push($urlCustom, 'ground-condition' . '/' . strtolower($option['label']));
            }
        }
        foreach ($rigModels as $rigModel) {
            $manufacture = $helperManufacture->getAmastyOptionSetting($rigModel->getValue());
            $manufacture = strtolower($manufacture);
            $urlLinkFirst = $blockMegamenu->translitUrl($manufacture);
            $urlLinkLast = $blockMegamenu->translitUrl($rigModel->getLabel().'-'.$rigModel->getValue());
            if($urlLinkFirst && $urlLinkLast) {
                $urlManufacture = 'shopby/rig/index/' . $urlLinkFirst . '/' . $urlLinkLast;
                array_push($urlCustom, $urlManufacture);
            }
        }


        $collection = $this->objectManager->create('\Magento\Framework\Data\Collection');
        foreach ($urlCustom as $item) {
            $varienObject = new \Magento\Framework\DataObject();
            $varienObject->setData([
                'url' => $item
            ]);
            $collection->addItem($varienObject);
        }
        $this->_sitemapItems[] = new \Magento\Framework\DataObject(
            [
                'collection' => $collection,
            ]
        );

        $this->_tags = [
            self::TYPE_INDEX => [
                self::OPEN_TAG_KEY => '<?xml version="1.0" encoding="UTF-8"?>' .
                    PHP_EOL .
                    '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
                    PHP_EOL,
                self::CLOSE_TAG_KEY => '</sitemapindex>',
            ],
            self::TYPE_URL => [
                self::OPEN_TAG_KEY => '<?xml version="1.0" encoding="UTF-8"?>' .
                    PHP_EOL .
                    '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' .
                    ' xmlns:content="http://www.google.com/schemas/sitemap-content/1.0"' .
                    ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' .
                    PHP_EOL,
                self::CLOSE_TAG_KEY => '</urlset>',
            ],
        ];
    }

    /**
     * @return string
     */
    public function getUrlBaseSite()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        return $storeManager->getStore()->getBaseUrl();
    }
}