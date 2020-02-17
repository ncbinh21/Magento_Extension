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



namespace Mirasvit\SeoSitemap\Model;
use Magento\Framework\Model\AbstractModel;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Sitemap  extends \Magento\Sitemap\Model\Sitemap
{
    /**
     * @var \Mirasvit\SeoSitemap\Api\Config\CmsSitemapConfigInterface
     */
    protected $cmsSitemapConfig;

    /**
     * @var \Mirasvit\SeoSitemap\Api\Config\LinkSitemapConfigInterface
     */
    protected $linkSitemapConfig;

    /**
     * @var \Mirasvit\SeoSitemap\Helper\Data
     */
    protected $seoSitemapData;

     /**
      * @var \Magento\Framework\Module\Manager
      */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Mirasvit\SeoSitemap\Api\Config\CmsSitemapConfigInterface $cmsSitemapConfig
     * @param \Mirasvit\SeoSitemap\Api\Config\LinkSitemapConfigInterface $linkSitemapConfig
     * @param \Mirasvit\SeoSitemap\Helper\Data $seoSitemapData
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Sitemap\Helper\Data $sitemapData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory $categoryFactory
     * @param \Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory $productFactory
     * @param \Magento\Sitemap\Model\ResourceModel\Cms\PageFactory $cmsFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $modelDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
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
        $this->cmsSitemapConfig = $cmsSitemapConfig;
        $this->linkSitemapConfig = $linkSitemapConfig;
        $this->seoSitemapData = $seoSitemapData;
        parent::__construct($context,
            $registry,
            $escaper,
            $sitemapData,
            $filesystem,
            $categoryFactory,
            $productFactory,
            $cmsFactory,
            $modelDate,
            $storeManager,
            $request,
            $dateTime,
            $resource,
            $resourceCollection,
            $data
        );
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
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
     * @return bool
     */
    public function isKeepSitemapInPubFolder()
    {
        if (isset($_SERVER['SCRIPT_FILENAME'])
            && strpos($_SERVER['SCRIPT_FILENAME'], '/pub/index.php') !== false) {
             return true;
        }

        return false;
    }

    /**
     * Add pub in path
     *
     * @return string
     */
    public function getPreparedSitemapPath()
    {
        $path = $this->getSitemapPath();
		// fix forder meida for cloud
        if (is_dir("./media")) {
	        $path = $this->getSitemapPath();
	        $this->setData('sitemap_path', $path);
        } else {
	        if ($this->isKeepSitemapInPubFolder()
		        && strpos($path, '/pub/') === false) {
		        $path = '/pub' . $this->getSitemapPath();
		        $this->setData('sitemap_path', $path);
	        }
        }

        return $path;
    }

    /**
     * Check sitemap file location and permissions
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $path = $this->getPreparedSitemapPath();

        /**
         * Check path is allow
         */
        if ($path && preg_match('#\.\.[\\\/]#', $path)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please define a correct path.'));
        }
        /**
         * Check exists and writable path
         */
        if (!$this->_directory->isExist($path)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'Please create the specified folder "%1" before saving the sitemap.',
                    $this->_escaper->escapeHtml($this->getSitemapPath())
                )
            );
        }

        if (!$this->_directory->isWritable($path)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Please make sure that "%1" is writable by the web-server.', $this->getSitemapPath())
            );
        }
        /**
         * Check allow filename
         */
        if (!preg_match('#^[a-zA-Z0-9_\.]+$#', $this->getSitemapFilename())) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'Please use only letters (a-z or A-Z), numbers (0-9) or underscores (_)'
                    . ' in the filename. No spaces or other characters are allowed.'
                )
            );
        }
        if (!preg_match('#\.xml$#', $this->getSitemapFilename())) {
            $this->setSitemapFilename($this->getSitemapFilename() . '.xml');
        }

        $this->setSitemapPath(rtrim(str_replace(str_replace('\\', '/', $this->_getBaseDir()), '', $path), '/') . '/');

        return AbstractModel::beforeSave();
    }

    /**
     * Get cms pages
     * @param  int $storeId
     * @return array
     */
    protected function getCmsPages($storeId)
    {
        $ignore = $this->cmsSitemapConfig->getIgnoreCmsPages();
        $links = $this->linkSitemapConfig->getAdditionalLinks($storeId);
        $cmsPages = $this->_cmsFactory->create()->getCollection($storeId);
        foreach ($cmsPages as $cmsKey => $cms) {
            if (in_array($cms->getUrl(), $ignore)) {
                unset($cmsPages[$cmsKey]);
            }
            if ($cms->getUrl() == 'home') {
                $cms->setUrl('');
            }
        }

        if ($links) {
            $cmsPages = array_merge($cmsPages, $links);
        }

        return $cmsPages;
    }


    /**
     * Generate XML file
     *
     * @see http://www.sitemaps.org/protocol.html
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function generateXml()
    {
        $this->_initSitemapItems();
        $excludedlinks = $this->linkSitemapConfig->getExcludeLinks();
        $redirectHelper = $this->getRedirectHelper();
        $progressBarSingle = false;

        if (php_sapi_name() == "cli") {
            // In cli-mode
            $output = new ConsoleOutput();
            $progressBarSingle = new ProgressBar($output);
            $progressBarSingle->setProgressCharacter('#');
            $progressBarSingle->setRedrawFrequency(100);
            $progressBarSingle->setFormat('%current% %bar% %memory:2s%');
            print "\n";
            $progressBarSingle->start();
        }

        /** @var $sitemapItem \Magento\Framework\DataObject */
        foreach ($this->_sitemapItems as $sitemapItem) {
            $changefreq = $sitemapItem->getChangefreq();
            $priority = $sitemapItem->getPriority();

            foreach ($sitemapItem->getCollection() as $item) {
                if ($this->seoSitemapData->checkArrayPattern($item->getUrl(), $excludedlinks)) {
                    continue;
                }
                if ($redirectHelper) {
                    $url = $redirectHelper->getUrlWithCorrectEndSlash($item->getUrl());
                    $item->setUrl($url);
                }
                $xml = $this->_getSitemapRow(
                    $item->getUrl(),
                    $item->getUpdatedAt(),
                    $changefreq,
                    $priority,
                    $item->getImages()
                );
                if ($this->_isSplitRequired($xml) && $this->_sitemapIncrement > 0) {
                    $this->_finalizeSitemap();
                }
                if (!$this->_fileSize) {
                    $this->_createSitemap();
                }
                $this->_writeSitemapRow($xml);
                // Increase counters
                $this->_lineCount++;
                $this->_fileSize += strlen($xml);
                if ($progressBarSingle) {
                    $progressBarSingle->advance();
                }
            }
        }
        if ($progressBarSingle) {
            $progressBarSingle->finish();
            $output->write("\033[1A");
        }
        $this->_finalizeSitemap();

        if ($this->_sitemapIncrement == 1) {
            // In case when only one increment file was created use it as default sitemap
            $path = rtrim(
                $this->getSitemapPath(),
                '/'
            ) . '/' . $this->_getCurrentSitemapFilename(
                $this->_sitemapIncrement
            );
            $destination = rtrim($this->getSitemapPath(), '/') . '/' . $this->getSitemapFilename();

            $this->_directory->renameFile($path, $destination);
        } else {
            // Otherwise create index file with list of generated sitemaps
            $this->_createSitemapIndex();
        }

        // Push sitemap to robots.txt
        if ($this->_isEnabledSubmissionRobots()) {
            $this->_addSitemapToRobotsTxt($this->getSitemapFilename());
        }

        $this->setSitemapTime($this->_dateModel->gmtDate('Y-m-d H:i:s'));
        $this->save();

        return $this;
    }

    /**
     * Get sitemap row (fix magento 2.2.2 bug for multistores) 
     *
     * @param string $url
     * @param null|string $lastmod
     * @param null|string $changefreq
     * @param null|string $priority
     * @param null|array|\Magento\Framework\DataObject $images
     * @return string
     * Sitemap images
     * @see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=178636
     *
     * Sitemap PageMap
     * @see http://support.google.com/customsearch/bin/answer.py?hl=en&answer=1628213
     */
    protected function _getSitemapRow($url, $lastmod = null, $changefreq = null, $priority = null, $images = null)
    {
        $url = $this->_getUrl($url);
        $row = '<loc>' . htmlspecialchars($url) . '</loc>';
        if ($lastmod) {
            $row .= '<lastmod>' . $this->_getFormattedLastmodDate($lastmod) . '</lastmod>';
        }
        if ($changefreq) {
            $row .= '<changefreq>' . $changefreq . '</changefreq>';
        }
        if ($priority) {
            $row .= sprintf('<priority>%.1f</priority>', $priority);
        }
        if ($images) {
            // Add Images to sitemap
            foreach ($images->getCollection() as $image) {
                $row .= '<image:image>';
                $row .= '<image:loc>' . htmlspecialchars($this->_getMediaUrl($image->getUrl())) . '</image:loc>';
                $row .= '<image:title>' . htmlspecialchars($images->getTitle()) . '</image:title>';
                if ($image->getCaption()) {
                    $row .= '<image:caption>' . htmlspecialchars($image->getCaption()) . '</image:caption>';
                }
                $row .= '</image:image>';
            }
            // Add PageMap image for Google web search
            $row .= '<PageMap xmlns="http://www.google.com/schemas/sitemap-pagemap/1.0"><DataObject type="thumbnail">';
            $row .= '<Attribute name="name" value="' . htmlspecialchars($images->getTitle()) . '"/>';
            $row .= '<Attribute name="src" value="' . htmlspecialchars(
                    $this->_getMediaUrl($images->getThumbnail())
                ) . '"/>';
            $row .= '</DataObject></PageMap>';
        }

        return '<url>' . $row . '</url>';
    }

    /**
     * Get media url
     *
     * @param string $url
     * @return string
     */
    protected function _getMediaUrl($url)
    {
        if (strpos($url, 'http://') !== false
            || strpos($url, 'https://') !== false) {
                $baseUrl = $this->_storeManager->getStore($this->getStoreId())
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                if (strpos($url, $baseUrl) === false) {
                    $url = preg_replace('/(.*?)\\/media\\//ims', $baseUrl . '/', $url, 1);
                }
                $urlExploded = explode('://', $url);
                $urlExploded[1] = str_replace('//', '/', $urlExploded[1]);
                $url = implode('://', $urlExploded);
                $url = str_replace('/pub/', '/', $url);

                return $url;
        }
        return $this->_getUrl($url, \Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @return \Mirasvit\Seo\Helper\Redirect|bool
     */
    public function getRedirectHelper()
    {
        return $this->moduleManager->isEnabled('Mirasvit_Seo') ?
               $this->objectManager->get('\Mirasvit\Seo\Helper\Redirect') :
               false ;
    }

    /**
     * Get sitemap index row
     * Don't delete. Need for correct path generation if sitemap splitted.
     *
     * @param string $sitemapFilename
     * @param null|string $lastmod
     * @return string
     */
    protected function _getSitemapIndexRow($sitemapFilename, $lastmod = null)
    {
        $url = $this->getSitemapUrl($this->getSitemapPath(), $sitemapFilename);
        $row = '<loc>' . htmlspecialchars($url) . '</loc>';
        if ($lastmod) {
            $row .= '<lastmod>' . $this->_getFormattedLastmodDate($lastmod) . '</lastmod>';
        }

        return '<sitemap>' . $row . '</sitemap>';
    }

}
