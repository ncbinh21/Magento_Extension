<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Index;

use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\BucketBuilderInterface;

/**
 * Class SearchAdapterTermAddDataPlugin
 * @package Amasty\Shopby\Plugin\Index
 */
class SearchAdapterTermAddDataPlugin
{
    /**
     * @var BucketBuilderInterface[]
     */
    private $bucketBuilders = [];

    /**
     * SearchAdapterTermAddDataPlugin constructor.
     * @param array $bucketBuilders
     */
    public function __construct(array $bucketBuilders = [])
    {
        $this->bucketBuilders = $bucketBuilders;
    }

    /**
     * @param mixed $subject
     * @param callable $proceed
     * @param RequestBucketInterface $bucket
     * @param array $dimensions
     * @param array $queryResult
     * @param DataProviderInterface $dataProvider
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundBuild(
        $subject,
        callable $proceed,
        RequestBucketInterface $bucket,
        array $dimensions,
        array $queryResult,
        DataProviderInterface $dataProvider
    ) {
        if (isset($this->bucketBuilders[$bucket->getField()])) {
            $builder = $this->bucketBuilders[$bucket->getField()];
            if ($builder instanceof BucketBuilderInterface) {
                return $builder->build($bucket, $dimensions, $queryResult, $dataProvider);
            }
        }
        return $proceed($bucket, $dimensions, $queryResult, $dataProvider);
    }
}
