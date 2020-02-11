<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\Component\Listing\Column\Giftcard;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Recipient
 *
 * @package Aheadworks\Giftcard\Ui\Component\Listing\Column\Giftcard
 */
class Recipient extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CustomerRepositoryInterface $customerRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        foreach ($dataSource['data']['items'] as &$item) {
            try {
                /** @var CustomerInterface $customer */
                $customer = $this->customerRepository->get($item['recipient_email']);
                $item[$fieldName . '_url'] = $this->context->getUrl(
                    'customer/index/edit',
                    ['id' => $customer->getId()]
                );
            } catch (NoSuchEntityException $e) {
            }
            $item[$fieldName . '_label'] = $item[$fieldName];
        }
        return $dataSource;
    }
}
