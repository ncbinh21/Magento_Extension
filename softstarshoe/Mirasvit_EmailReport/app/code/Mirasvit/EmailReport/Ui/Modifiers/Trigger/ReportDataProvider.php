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
 * @package   mirasvit/module-email-report
 * @version   1.0.5
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Ui\Modifiers\Trigger;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Mirasvit\EmailReport\Api\Repository\RepositoryInterface;
use Mirasvit\EmailReport\Api\Service\AggregatorServiceInterface;

class ReportDataProvider implements ModifierInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var AggregatorServiceInterface
     */
    private $aggregator;
    /**
     * @var array
     */
    private $reportRepositories;

    /**
     * Segment constructor.
     *
     * @param UrlInterface               $urlBuilder
     * @param ArrayManager               $arrayManager
     * @param StoreManagerInterface      $storeManager
     * @param AggregatorServiceInterface $aggregator
     * @param RepositoryInterface[]      $reportRepositories
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        StoreManagerInterface $storeManager,
        AggregatorServiceInterface $aggregator,
        array $reportRepositories = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
        $this->storeManager = $storeManager;
        $this->aggregator = $aggregator;
        $this->reportRepositories = $reportRepositories;
    }

    /**
     * {@inheritDoc}
     */
    public function modifyData(array $data)
    {
        foreach ($this->reportRepositories as $repository) {
            $collection = $repository->getCollection();
            $fieldName = str_replace('_id', '_qty', $collection->getResource()->getIdFieldName());

            $data[$fieldName] = $this->aggregator->count(
                $collection,
                $data['id_field_name'],
                $data[$data['id_field_name']]
            );
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
