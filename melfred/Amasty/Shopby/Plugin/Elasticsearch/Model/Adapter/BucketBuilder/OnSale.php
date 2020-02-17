<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\BucketBuilder;

use Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\BucketBuilderInterface as BucketBuilderInterface;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use Magento\Framework\Search\Dynamic\DataProviderInterface;

/**
 * Class IsNew
 * @package Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\BucketBuilder
 */
class OnSale implements BucketBuilderInterface
{
    const ON_SALE_INDEX = 1;

    /**
     * @param RequestBucketInterface $bucket
     * @param array $queryResult
     * @return array
     */
    public function build(
        RequestBucketInterface $bucket,
        array $queryResult
    ) {
        $values = [];
        foreach ($queryResult['aggregations'][$bucket->getName()]['buckets'] as $resultBucket) {
            $values[self::ON_SALE_INDEX] = [
                'value' => self::ON_SALE_INDEX,
                'count' => $resultBucket['doc_count'],
            ];
        }
        return $values;
    }
}
