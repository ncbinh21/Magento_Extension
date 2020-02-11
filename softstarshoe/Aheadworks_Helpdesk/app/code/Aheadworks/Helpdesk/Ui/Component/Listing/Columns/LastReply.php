<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Helpdesk\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class LastReply
 * @package Aheadworks\Helpdesk\Ui\Component\Listing\Columns
 */
class LastReply extends \Aheadworks\Helpdesk\Ui\Component\Listing\Columns\Customer
{
    /**
     * Customer repository model (by default)
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        array $components = [],
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        parent::__construct($context, $uiComponentFactory, $customerRepository, $storeRepository, $components, $data);
    }

    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            if ($item['last_reply_type'] == \Aheadworks\Helpdesk\Model\ThreadMessage::OWNER_CUSTOMER_VALUE) {
                $item['last_reply_by'] = parent::prepareContent(
                    $item['customer_id'],
                    $item['customer_name'],
                    $item['customer_email'],
                    $item['store_id']
                );
            }
        }
        return $dataSource;
    }
}
