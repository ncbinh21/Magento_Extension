<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: scratchwizard.local
 */

namespace Forix\Custom\Rewrite\Mirasvit\SeoSitemap\Model;


class Sitemap extends \Mirasvit\SeoSitemap\Model\Sitemap
{
    protected function _initSitemapItems()
    {
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

        if ($this->moduleManager->isEnabled('Aheadworks_Blog')) {
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
            if ($postItems = $blogMxSitemap->getPostItems()) {
                $this->_sitemapItems[] = $postItems;
            }
        }

        if ($this->moduleManager->isEnabled('FishPig_WordPress')) {
            /** @var $resource \Magento\Framework\App\ResourceConnection */
            $resource = $this->objectManager->create('\Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $select = $connection->select()
                ->from(['p'=>$resource->getTableName('softstar_wp_terms')],[])
                ->join(['c'=>$resource->getTableName('softstar_wp_term_taxonomy')],'c.term_id=p.term_id',[])
                ->where('c.taxonomy=?','category');
            $select->columns(['url'=>new \Zend_Db_Expr('CONCAT("live-bare-blog/category/",p.slug)'),'updated_at'=>new \Zend_Db_Expr('CURDATE()')]);
            $query = $connection->query($select);
            $collection = $this->objectManager->create('\Magento\Framework\Data\Collection');
            while($row = $query->fetch()){
                $varienObject = new \Magento\Framework\DataObject();
                $varienObject->setData($row);
                $collection->addItem($varienObject);
            }
            $this->_sitemapItems[] = new \Magento\Framework\DataObject(
                [
                    'collection' => $collection,
                ]
            );
//            $select = $connection->select()
//                ->from(['p'=>$resource->getTableName('softstar_wp_posts')],[])
//                ->where('p.post_status=?','publish')
//                ->where('p.post_type=?','post');
//            $select->columns(['url'=>new \Zend_Db_Expr('CONCAT("live-bare-blog/",p.post_name)'), 'updated_at'=>'post_date']);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
            $urlBuilder = $objectManager->create('\FishPig\WordPress\Model\App\Url');
            $collectionFish = $objectManager->create('FishPig\WordPress\Model\ResourceModel\Post\Collection');
            $sql = $collectionFish->getResource()->getPermalinkSqlColumn();

            $collectionFish->addFieldToFilter('post_status', 'publish')->addFieldToFilter('post_type', 'post');
            $collectionFish->getSelect()->reset(\Zend_Db_Select::COLUMNS);
            $collectionFish->getSelect()->columns(array('url' =>  $sql , 'post_date'));
            $collection = $this->objectManager->create('\Magento\Framework\Data\Collection');

            $urlBaseSite = $this->getUrlBaseSite();
            foreach ($collectionFish as $item) {
                //print_r($item->debug());
                $varienObject = new \Magento\Framework\DataObject();
                $varienObject->setData([
                    'url' => str_replace($urlBaseSite,'',$urlBuilder->getUrl($item->getUrl())),
                    'updated_at' => $item->getPostDate()
                ]);
                $collection->addItem($varienObject);
            }
            $urlCustom = [
                '',
                'live-bare-blog',
                'fanphoto',
                'contact',
                'checkout/cart',
                'sales/guest/form'
            ];
            foreach ($urlCustom as $item) {
                $varienObject = new \Magento\Framework\DataObject();
                $varienObject->setData([
                    'url' => $item
                ]);
                $collection->addItem($varienObject);
            }

//            $collectionFish->getSelect()->columns(['url'=> 'CONCAT("live-bare-blog/",p.post_name)']);

//            $collectionFish->columns(['url'=>new \Zend_Db_Expr('CONCAT("live-bare-blog/",p.post_name)'), 'updated_at'=>'post_date']);
            $this->_sitemapItems[] = new \Magento\Framework\DataObject(
                [
                    'collection' => $collection,
                ]
            );
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
     * @return string
     */
    public function getUrlBaseSite()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        return $storeManager->getStore()->getBaseUrl();
    }
}