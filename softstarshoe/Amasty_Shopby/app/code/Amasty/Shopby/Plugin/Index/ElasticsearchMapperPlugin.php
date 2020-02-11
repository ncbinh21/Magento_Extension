<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Index;

use Amasty\Shopby\Model\Search\RequestGenerator as ShopbyRequestGenerator;

class ElasticsearchMapperPlugin
{
    /**
     *
     * @param \Magento\Elasticsearch\SearchAdapter\Mapper $subject
     * @param array $query
     * @return array
     */
    public function afterBuildQuery($subject, array $query)
    {
        $query = $this->fixAggregationSizeLimitedResponse($query);

        if (!isset($query['body']['query']['bool'])) {
            return $query;
        }

        return $this->adjustRequestQuery($query);
    }

    /**
     * By default it is not more than 10 options per filter
     *
     * @param array $query
     * @return array
     */
    private function fixAggregationSizeLimitedResponse(array $query)
    {
        foreach ($query['body']['aggregations'] as &$bucket) {
            if (isset($bucket['terms'])) {
                $bucket['terms']['size'] = '0';
            }
        }

        return $query;
    }

    /**
     * Update a request query. In case it contains values from "MULTIPLY SELECTION" + "AND CONDITION" filter.
     *
     * @param array $query
     * @return array
     */
    private function adjustRequestQuery(array $query)
    {
        $queryBool = $query['body']['query']['bool'];
        $updatedQueryBool = $this->getQueryWithNodesInRightPlaces($queryBool);
        $query['body']['query']['bool'] = $updatedQueryBool;

        return $query;
    }

    /**
     * @param $queryBool
     * @return array
     */
    private function getQueryWithNodesInRightPlaces($queryBool)
    {
        if (!isset($queryBool['should']) || !is_array($queryBool['should'])) {
            return $queryBool;
        }

        foreach ($queryBool['should'] as $index => &$node) {
            //there could be either "terms" or "term" unify it
            if (isset($node['terms'])) {
                $node['term'] = $node['terms'];
            }

            if (!isset($node['term']) || !is_array($node['term'])) {
                continue;
            }

            $moved = $this->removeFakeSuffixFromNode($node);

            //restore unified "term" to "terms"
            if (isset($node['terms'])) {
                $node['terms'] = $node['term'];
                unset($node['term']);
            }

            if ($moved) {
                $queryBool['must'][] = $node;
                unset($queryBool['should'][$index]);
            }
        }

        return $queryBool;
    }

    /**
     * Replace ['attribute_' . FAKE_SUFFIX . $i => $value] with ['attribute' => $value].
     *
     * @param array $node
     * @return int
     */
    private function removeFakeSuffixFromNode(array &$node)
    {
        $moved = 0;
        foreach ($node['term'] as $code => $value) {
            $pos = strpos($code, ShopbyRequestGenerator::FAKE_SUFFIX);
            if ($pos !== false) {
                $correctedAttrCode = substr($code, 0, $pos);
                $node['term'][$correctedAttrCode] = $value;
                unset($node['term'][$code]);
                $moved++;
            }
        }

        return $moved;
    }
}
