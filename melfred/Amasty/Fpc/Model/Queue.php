<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Amasty\Fpc\Api\QueuePageRepositoryInterface;
use Amasty\Fpc\Exception\LockException;
use Amasty\Fpc\Helper\Http as HttpHelper;
use Amasty\Fpc\Model\Queue\Page;
use Magento\Customer\Model\Group;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\File\WriteInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Amasty\Fpc\Model\Config\Source\QuerySource;

class Queue
{
    const LOCK_FILE = 'amasty_fpc_crawler.lock';
    const DEFAULT_VALUE = null;

    /**
     * @var WriteInterface
     */
    protected $lockFile;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Log
     */
    private $crawlerLog;
    /**
     * @var ResourceModel\Queue\Page\CollectionFactory
     */
    private $pageCollectionFactory;
    /**
     * @var \Amasty\Fpc\Model\QueuePageRepository
     */
    private $pageRepository;
    /**
     * @var Crawler
     */
    private $crawler;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Source\Factory
     */
    private $sourceFactory;

    public function __construct(
        Filesystem $filesystem,
        Config $config,
        Log $crawlerLog,
        ResourceModel\Queue\Page\CollectionFactory $pageCollectionFactory,
        QueuePageRepositoryInterface $pageRepository,
        Crawler $crawler,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        Source\Factory $sourceFactory
    ) {
        $this->filesystem = $filesystem;
        $this->config = $config;
        $this->crawlerLog = $crawlerLog;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->pageRepository = $pageRepository;
        $this->crawler = $crawler;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->sourceFactory = $sourceFactory;
    }

    protected function lock()
    {
        $directoryWrite = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);

        $this->lockFile = $directoryWrite->openFile(self::LOCK_FILE);

        if (!$this->lockFile->lock(LOCK_EX | LOCK_NB)) {
            throw new LockException(__('Another lock detected'));
        }
    }

    protected function unlock()
    {
        $this->lockFile->unlock();
    }

    /**
     * Generate queue to crawl
     *
     * @return bool
     */
    public function generate()
    {
        $this->lock();

        $this->pageRepository->clear();

        $queueLimit = $this->config->getValue('crawler/max_queue_size');

        $source = $this->getSource($queueLimit);

        if (count($source) > $queueLimit) {
            $source = array_slice($source, 0, $queueLimit);
        }

        foreach ($source as $item) {
            $this->pageRepository->addPage($item);
        }

        $this->unlock();

        return true;
    }

    protected function getCombinations()
    {
        $stores = $this->config->getStores();
        $currencies = $this->config->getCurrencies();
        $customerGroups = $this->config->getCustomerGroups();

        /** @var Store $defaultStore */
        $defaultStore = $this->storeManager->getWebsite()->getDefaultStore();
        $defaultCurrency = $defaultStore->getDefaultCurrency()->getCode();

        // Replace default values with empty

        $this
            ->replaceDefaultValue($customerGroups, Group::NOT_LOGGED_IN_ID)
            ->replaceDefaultValue($currencies, $defaultCurrency)
            ->replaceDefaultValue($stores, $defaultStore->getId());

        return [$stores, $currencies, $customerGroups];
    }

    protected function replaceDefaultValue(&$values, $default)
    {
        $key = array_search($default, $values);

        if (false !== $key) {
            unset($values[$key]);
            array_unshift($values, self::DEFAULT_VALUE);
        }

        // Add default value if nothing selected
        if (empty($values)) {
            array_unshift($values, self::DEFAULT_VALUE);
        }

        return $this;
    }

    public function process()
    {
        $this->lock();

        $batchSize = (int)$this->config->getValue('crawler/batch_size');

        /** @var ResourceModel\Queue\Page\Collection $pages */
        $pages = $this->pageCollectionFactory->create();
        $pages->setOrder('rate', 'DESC');

        $this->crawlerLog->trim();

        list($stores, $currencies, $customerGroups) = $this->getCombinations();

        $pagesCrawled = 0;

        /** @var Page $page */
        foreach ($pages as $page) {
            if ($pagesCrawled >= $batchSize) {
                break;
            }

            $pagesCrawled += $this->processCombinations(
                $page,
                $page->getStore() ? [$page->getStore()] : $stores,
                $currencies,
                $customerGroups
            );

            $this->pageRepository->delete($page);
        }

        $this->unlock();

        return true;
    }

    /**
     * Process all page combinations and return count of actually crawled pages
     *
     * @param $page
     * @param $stores
     * @param $currencies
     * @param $customerGroups
     *
     * @return int
     */
    public function processCombinations(
        $page,
        $stores,
        $currencies,
        $customerGroups
    ) {
        $pagesCrawled = 0;
        $delay = (int)$this->config->getValue('crawler/delay');

        foreach ($customerGroups as $customerGroup) {
            foreach ($stores as $store) {
                foreach ($currencies as $currency) {
                    $status = $this->crawler->processPage($page, $customerGroup, $store, $currency);
                    usleep($delay * 1000);

                    if ($status != HttpHelper::STATUS_ALREADY_CACHED) {
                        $pagesCrawled++;
                    }
                }
            }
        }

        return $pagesCrawled;
    }

    /**
     * Return pages to crawl from selected source
     *
     * @param int $queueLimit
     *
     * @return array
     */
    public function getSource($queueLimit)
    {
        $type = $this->config->getValue('crawler/source');

        if (QuerySource::SOURCE_COMBINE == $type) {
            $eMessage = __('Combine is selected as source: ');
            try {
                $filePages = $this->sourceFactory->create(QuerySource::SOURCE_TEXT_FILE)->getPages($queueLimit, $eMessage);
            } catch (\Exception $e) {
                $this->logger->warning($eMessage . $e->getMessage());
                $filePages = [];
            }

            $queueLimit = $queueLimit - count($filePages);

            try {
                $sitemapPages = $this->sourceFactory->create(QuerySource::SOURCE_SITE_MAP)->getPages($queueLimit, $eMessage);
            } catch (\Exception $e) {
                $this->logger->warning($eMessage . $e->getMessage());
                $sitemapPages = [];
            }

            $source = array_merge($filePages, $sitemapPages);
        } else {
            $eMessage = '';
            switch ($type) {
                case QuerySource::SOURCE_TEXT_FILE:
                    $eMessage = __('File is selected as source: ');
                    break;
                case QuerySource::SOURCE_SITE_MAP:
                    $eMessage = __('Sitemap XML is selected as source: ');
                    break;
            }

            try {
                $source = $this->sourceFactory->create($type)->getPages($queueLimit, $eMessage);
            } catch (\Exception $e) {
                $this->logger->warning($eMessage . $e->getMessage());

                return [];
            }
        }

        if (empty($source)) {
            $this->logger->warning($eMessage . __('but source is empty'));

            return [];
        }

        foreach ($source as $k => &$item) {
            if ($this->isIgnored($item['url'])) {
                unset($source[$k]);
            }
        }

        usort($source, function($a, $b) {
            if ($a['rate'] < $b['rate']) {
                return 1;
            } else if ($a['rate'] > $b['rate']) {
                return -1;
            } else {
                return 0;
            }
        });

        return $source;
    }

    public function isIgnored($path)
    {
        $ignoreList = $this->config->getValue('crawler/ignore_list');
        $ignoreList = preg_split('|[\r\n]+|', $ignoreList, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($ignoreList as $pattern) {
            if (preg_match("|$pattern|", $path)) {
                return true;
            }
        }

        return false;
    }
}
