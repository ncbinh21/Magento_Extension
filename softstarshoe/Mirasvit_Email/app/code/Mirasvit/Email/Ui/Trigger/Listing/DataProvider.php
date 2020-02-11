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
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Ui\Trigger\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Mirasvit\Email\Api\Data\TriggerInterface;
use Mirasvit\EmailReport\Api\Data\OpenInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as BaseDataProvider;

class DataProvider extends BaseDataProvider
{
    /**
     * @var PoolInterface
     */
    private $poolInterface;

    /**
     * DataProvider constructor.
     *
     * @param string                $name
     * @param string                $primaryFieldName
     * @param string                $requestFieldName
     * @param ReportingInterface    $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface      $request
     * @param FilterBuilder         $filterBuilder
     * @param PoolInterface         $poolInterface
     * @param array                 $meta
     * @param array                 $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        PoolInterface $poolInterface,
        array $meta = [],
        array $data = []
    ) {
        $this->poolInterface = $poolInterface;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request,
            $filterBuilder, $meta, $data);
    }


    /**
     * {@inheritdoc}
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $result = [];

        $result['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $data = [];
            foreach ($item->getCustomAttributes() as $attribute) {
                $data[$attribute->getAttributeCode()] = $attribute->getValue();
            }

            // modify trigger's data
            foreach ($this->poolInterface->getModifiersInstances() as $modifier) {
                $data = $modifier->modifyData($data);
            }

            $result['items'][] = $data;
        }

        $result['totalRecords'] = $searchResult->getTotalCount();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getSearchResult()
    {
        /** @var $searchResult \Mirasvit\Email\Model\ResourceModel\Trigger\Grid */
        $searchResult = parent::getSearchResult();
        /*$searchResult->getSelect()
            ->joinLeft(['opens' => $searchResult->getTable(OpenInterface::TABLE_NAME)],
                'opens.trigger_id = main_table.trigger_id',
                ['open_qty' => 'COUNT(opens.entity_id)']
            )
            ->group('main_table.'.TriggerInterface::ID);*/

        return $searchResult;
    }


}
