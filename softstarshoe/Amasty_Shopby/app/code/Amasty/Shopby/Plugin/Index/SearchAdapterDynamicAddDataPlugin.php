<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Index;

use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use \Magento\Elasticsearch\SearchAdapter\Aggregation\Builder\Dynamic as DynamicBuilder;

class SearchAdapterDynamicAddDataPlugin
{
    /**
     * @param DynamicBuilder $builder
     * @param \Closure $closure
     * @param RequestBucketInterface $bucket
     * @param array $dimensions
     * @param array $queryResult
     * @param DataProviderInterface $dataProvider
     * @return array
     */
    public function aroundBuild(
        $builder,
        \Closure $closure,
        RequestBucketInterface $bucket,
        array $dimensions,
        array $queryResult,
        DataProviderInterface $dataProvider
    ) {
        $resultData = $closure($bucket, $dimensions, $queryResult, $dataProvider);

        //used in a slider for price and decimal
        $resultData['data']['value'] = 'data';
        $resultData['data']['min'] = $queryResult['aggregations'][$bucket->getName()]['min'];
        $resultData['data']['max'] = $queryResult['aggregations'][$bucket->getName()]['max'];
        $resultData['data']['count'] = $queryResult['aggregations'][$bucket->getName()]['count'];

        return $resultData;
    }
}
