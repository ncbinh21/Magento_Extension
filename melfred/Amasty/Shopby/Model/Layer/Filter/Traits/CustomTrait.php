<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Model\Layer\Filter\Traits;

trait CustomTrait
{
    use FilterTrait;

    /**
     * @return \Magento\Framework\Search\Response\QueryResponse|\Magento\Framework\Search\ResponseInterface|null
     */
    private function getAlteredQueryResponse()
    {
        $alteredQueryResponse = null;
        if ($this->hasCurrentValue()) {
            $productCollection = $this->getLayer()->getProductCollection();
            $requestBuilder = clone $productCollection->getMemRequestBuilder();
            $requestBuilder->removePlaceholder($this->attributeCode);
            $queryRequest = $requestBuilder->create();
            $alteredQueryResponse = $this->searchEngine->search($queryRequest);
        }

        return $alteredQueryResponse;
    }

    /**
     * @return array
     */
    private function getFacetedData()
    {
        $collection = $this->getLayer()->getProductCollection();
        $alteredQueryResponse = $this->getAlteredQueryResponse();
        $optionsFacetedData = $collection->getFacetedData($this->attributeCode, $alteredQueryResponse);
        return $optionsFacetedData;
    }
}
