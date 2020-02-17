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


namespace Mirasvit\Seo\Console\Command;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\State;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ApplyProductUrlKeyTemplateCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection
     */
    protected $urlRewriteProduct;

     /**
      * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
      */
    protected $productCollectionFactory;

    /**
     * @var \Mirasvit\Seo\Model\SeoObject\ProducturlFactory
     */
    protected $objectProducturlFactory;

    /**
     * @var \Mirasvit\Seo\Api\Service\FriendlyUrl\ProductUrlKeyTemplateInterface
     */
    protected $productUrlKeyTemplate;

    /**
     * @var \Mirasvit\Seo\Api\Service\Cache\CleanInterface
     */
    protected $cleanCache;

    /**
     * @var string
     */
    protected $logPath = '/var/log/seoUrlKey.log';

    /**
     * Info
     * @var array
     */
    protected $info = ['info' => 'Info',
        'product-url-key-template-example' => 'Show process info without any changes (in fact, URLs will not be changed)',
        'restore-product-url-key-template' => 'Restore product url key template using [product_name]',
        'apply-product-url-key-template' => 'Apply product url key template',
    ];

    /**
     * @param State                  $appState
     * @param ObjectManagerInterface $objectManager
     * @param StoreFactory           $storeFactory
     */
    public function __construct(
        State $appState,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $urlRewrite,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Mirasvit\Seo\Model\SeoObject\ProducturlFactory $objectProducturlFactory,
        \Mirasvit\Seo\Api\Service\FriendlyUrl\ProductUrlKeyTemplateInterface $productUrlKeyTemplate,
        \Mirasvit\Seo\Api\Service\Cache\CleanInterface $cleanCache
    ) {
        $this->appState = $appState;
        $this->urlRewriteProduct = $urlRewrite->addFieldToFilter('entity_type', 'product')
                                                ->addFieldToFilter('redirect_type', 0)
                                                ->addFieldToFilter('metadata', ['null' => true]);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->objectProducturlFactory = $objectProducturlFactory;
        $this->productUrlKeyTemplate = $productUrlKeyTemplate;
        $this->cleanCache = $cleanCache;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
         $this->setName('mirasvit:seo')
            ->setDescription('SEO');

        $this->addOption('info', null, null, $this->info['info']);
        $this->addOption('product-url-key-template-example', null, null, $this->info['product-url-key-template-example']);
        $this->addOption('restore-product-url-key-template', null, null, $this->info['restore-product-url-key-template']);
        $this->addOption('apply-product-url-key-template', null, null, $this->info['apply-product-url-key-template']);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('info')) {
            echo 'php bin/magento mirasvit:seo --info   ' . $this->info['info'] . PHP_EOL;
            echo 'php bin/magento mirasvit:seo --product-url-key-template-example   ' . $this->info['product-url-key-template-example'] . PHP_EOL;
            echo 'php bin/magento mirasvit:seo --restore-product-url-key-template   ' . $this->info['restore-product-url-key-template'] . PHP_EOL;
            echo 'php bin/magento mirasvit:seo --apply-product-url-key-template   ' . $this->info['apply-product-url-key-template'] . PHP_EOL;

            echo 'Create tables dump: ' . PHP_EOL;
            echo 'mysqldump -uUSERNAME -pPASSWORD DBNAME [db_table_prefix]url_rewrite > url_rewrite.sql' . PHP_EOL;
            echo 'mysqldump -uUSERNAME -pPASSWORD DBNAME [db_table_prefix]catalog_product_entity_varchar > catalog_product_entity_varchar.sql' . PHP_EOL;
            echo '(before running, replace USERNAME to mysql username,
            PASSWORD to mysql password,
            DBNAME to store database name,
            [db_table_prefix] to database table prefix.
            It can be found in the file /app/etc/env.php)' . PHP_EOL;

            echo 'Recover tables from dump: ' . PHP_EOL;
            echo 'mysqldump -uUSERNAME -pPASSWORD [db_table_prefix]url_rewrite < url_rewrite.sql' . PHP_EOL;
            echo 'mysqldump -uUSERNAME -pPASSWORD [db_table_prefix]catalog_product_entity_varchar < catalog_product_entity_varchar.sql' . PHP_EOL;
            echo '(before running, replace USERNAME to mysql username,
            PASSWORD to mysql password,
            DBNAME to store database name,
            [db_table_prefix] to database table prefix.
            It can be found in the file /app/etc/env.php)' . PHP_EOL;

        }

        if ($input->getOption('product-url-key-template-example')
            || $input->getOption('restore-product-url-key-template')
            || $input->getOption('apply-product-url-key-template') ) {
                gc_collect_cycles();
            if (!$this->appState->isAreaCodeEmulated()) {
                $this->appState->setAreaCode('frontend');
            }
                $urlKeyTemplate = $this->productUrlKeyTemplate->getUrlKeyTemplate();

            if (!$urlKeyTemplate) {
                echo "Product URL Key Template is disabled in SEO configuration";
                return true;
            }

            foreach ($this->urlRewriteProduct as $rewrite) {
                $storeId = $rewrite->getStoreId();
                if (!isset($urlKeyTemplate[$storeId]) || !$urlKeyTemplate[$storeId]) {
                    continue;
                }
                $productId = $rewrite->getEntityId();
                $product = $this->productCollectionFactory->create()
                            ->addAttributeToSelect('*')
                            ->addFieldToFilter('entity_id', $productId)
                            ->setStoreId($storeId)
                            ->getFirstItem()
                            ->setStoreId($storeId);

                $templ = $this->objectProducturlFactory->create()
                                ->setProduct($product);
                if ($input->getOption('restore-product-url-key-template')) {
                    $urlKeyTemplate[$storeId] = '[product_name]';
                }
                $urlKey = $templ->parse($urlKeyTemplate[$storeId]);
                $urlKey = $product->formatUrlKey($urlKey);

                if ($urlKey == $product->getUrlKey()) {
                    echo 'product ID: ' . $productId . ' | store ID: ' . $storeId . ' | already use url key: ' . $urlKey . PHP_EOL;
                    continue;
                }

                $urlKeyUnique = $this->productUrlKeyTemplate->checkUrlKeyUnique($urlKey, $productId, $storeId);

                if ($urlKey && $urlKeyUnique === true) {
                    if (!$input->getOption('product-url-key-template-example')) { $this->productUrlKeyTemplate->applyUrlKey($urlKey, $product); 
                    }
                    $memUsage = memory_get_usage(true);
                    echo 'product ID: ' . $productId . ' | store ID: ' . $storeId
                    . ' | added url key: ' . $urlKey . ' | php memory: ' . round($memUsage/1048576, 2). 'Mb' . PHP_EOL;
                } elseif ($urlKey) {
                    $info = 'product ID: ' . $productId . ' | store ID: ' . $storeId
                    . ' | duplicate url key not added (this info has been added in file ' . $this->logPath . '): ' . $urlKey . PHP_EOL;
                    echo $info;
                    $this->log($info);
                } else {
                    $info = 'product ID: ' . $productId . ' | store ID: ' . $storeId
                    . ' | empty url key not added (this info has been added in file ' . $this->logPath . '): ' . $urlKey . PHP_EOL;
                    echo $info;
                    $this->log($info);
                }
            }

            if ($input->getOption('restore-product-url-key-template')
                    || $input->getOption('apply-product-url-key-template') ) {
                    $this->cleanCache->cleanAllCache();

            }
        }
    }

    protected function log($info) 
    {
        $writer = new \Zend\Log\Writer\Stream(BP . $this->logPath);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($info);
    }

}
