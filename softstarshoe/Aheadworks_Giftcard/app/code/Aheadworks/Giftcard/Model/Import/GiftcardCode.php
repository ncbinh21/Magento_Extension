<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Import;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Api\Data\GiftcardInterfaceFactory;
use Aheadworks\Giftcard\Model\ResourceModel\Validator\GiftcardIsUnique;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class GiftcardCode
 *
 * @package Aheadworks\Giftcard\Model\Import
 */
class GiftcardCode extends AbstractImport
{
    /**
     * {@inheritdoc}
     */
    protected $namespace = 'aw_giftcard_listing';

    /**
     * {@inheritdoc}
     */
    protected $logFileName = 'aw_gc_giftcard_codes_import';

    /**
     * @var GiftcardIsUnique
     */
    private $giftcardIsUniqueValidator;

    /**
     * GiftcardInterfaceFactory
     */
    private $giftcardFactory;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param Filter $filter
     * @param RequestInterface $request
     * @param UrlInterface $url
     * @param GiftcardIsUnique $giftcardIsUniqueValidator
     * @param GiftcardInterfaceFactory $giftcardFactory
     * @param TimezoneInterface $localeDate
     * @param OrderRepositoryInterface $orderRepository
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        Filter $filter,
        RequestInterface $request,
        UrlInterface $url,
        GiftcardIsUnique $giftcardIsUniqueValidator,
        GiftcardInterfaceFactory $giftcardFactory,
        TimezoneInterface $localeDate,
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($dataObjectHelper, $filter, $request, $url);
        $this->giftcardIsUniqueValidator = $giftcardIsUniqueValidator;
        $this->giftcardFactory = $giftcardFactory;
        $this->localeDate = $localeDate;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function convertDataToObject($filteredRows)
    {
        $giftcardCodes = [];
        foreach ($filteredRows as $row) {
            $row = $this->getRowData($row);
            /** @var GiftcardInterface $giftcard */
            $giftcard = $this->giftcardFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $giftcard,
                $row,
                GiftcardInterface::class
            );

            if ($this->giftcardIsUniqueValidator->validate($giftcard->getCode())) {
                $giftcardCodes[] = $this->prepareGiftcard($giftcard);
            } else {
                $this->addMessages([
                    __('Gift Card code %1 already in use', $giftcard->getCode())
                ]);
            }
        }
        return $giftcardCodes;
    }

    /**
     * {@inheritdoc}
     */
    protected function getHeaderFields()
    {
        return [
            ['header' => __('ID'), 'field_name' => GiftcardInterface::ID],
            ['header' => __('Created At'), 'field_name' => GiftcardInterface::CREATED_AT],
            ['header' => __('Order #'), 'field_name' => GiftcardInterface::ORDER_ID],
            ['header' => __('Product'), 'field_name' => GiftcardInterface::PRODUCT_ID],
            ['header' => __('Type'), 'field_name' => GiftcardInterface::TYPE, 'required' => true],
            ['header' => __('Code'), 'field_name' => GiftcardInterface::CODE, 'required' => true],
            ['header' => __('Initial Amount'), 'field_name' => GiftcardInterface::INITIAL_BALANCE],
            ['header' => __('Availability'), 'field_name' => GiftcardInterface::STATE],
            ['header' => __('Balance'), 'field_name' => GiftcardInterface::BALANCE],
            ['header' => __('Expiration Date'), 'field_name' => GiftcardInterface::EXPIRE_AT],
            ['header' => __('Sender Name'), 'field_name' => GiftcardInterface::SENDER_NAME, 'required' => true],
            [
                'header' => __('Sender Email'),
                'field_name' => GiftcardInterface::SENDER_EMAIL,
                'required' => true,
                'required_association' => [
                    ['field' => GiftcardInterface::TYPE, 'value' => __('Virtual')]
                ]
            ],
            ['header' => __('Recipient Name'), 'field_name' => GiftcardInterface::RECIPIENT_NAME, 'required' => true],
            [
                'header' => __('Recipient Email'),
                'field_name' => GiftcardInterface::RECIPIENT_EMAIL,
                'required' => true,
                'required_association' => [
                    ['field' => GiftcardInterface::TYPE, 'value' => __('Virtual')]
                ]
            ],
            ['header' => __('Delivery Date'), 'field_name' => GiftcardInterface::DELIVERY_DATE],
            ['header' => __('Delivery Date Timezone'), 'field_name' => GiftcardInterface::DELIVERY_DATE_TIMEZONE],
            ['header' => __('Email Sent'), 'field_name' => GiftcardInterface::EMAIL_SENT],
            ['header' => __('Email Template'), 'field_name' => GiftcardInterface::EMAIL_TEMPLATE],
            ['header' => __('Website'), 'field_name' => GiftcardInterface::WEBSITE_ID, 'required' => true]
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareValue($column, $value)
    {
        if ($column == GiftcardInterface::ORDER_ID) {
            if (!empty($value)) {
                $this->searchCriteriaBuilder
                    ->addFilter(OrderInterface::INCREMENT_ID, $value);
                $orders = $this->orderRepository->getList($this->searchCriteriaBuilder->create())->getItems();

                $value = null;
                if (!empty($orders)) {
                    $order = array_shift($orders);
                    $value = $order->getEntityId();
                }
            }
        }

        if ($column == GiftcardInterface::PRODUCT_ID) {
            if (!empty($value)) {
                $this->searchCriteriaBuilder
                    ->addFilter(ProductInterface::NAME, $value);
                $products = $this->productRepository->getList($this->searchCriteriaBuilder->create())->getItems();

                $value = null;
                if (!empty($products)) {
                    $product = array_shift($products);
                    $value = $product->getEntityId();
                }
            }
        }

        if ($column == GiftcardInterface::CREATED_AT) {
            $convertedDate = new \DateTime($value, new \DateTimeZone($this->localeDate->getConfigTimezone()));
            $convertedDate->setTimezone(new \DateTimeZone('UTC'));
            $value = $convertedDate->format(StdlibDateTime::DATETIME_PHP_FORMAT);
        }

        if ($column == GiftcardInterface::EXPIRE_AT) {
            if (!empty($value)) {
                $convertedDate = new \DateTime($value, new \DateTimeZone('UTC'));
                $value = $convertedDate->format(StdlibDateTime::DATETIME_PHP_FORMAT);
            }
        }

        if ($column == GiftcardInterface::DELIVERY_DATE) {
            if (!empty($value)) {
                $convertedDate = new \DateTime($value, new \DateTimeZone($this->localeDate->getConfigTimezone()));
                $convertedDate->setTimezone(new \DateTimeZone('UTC'));
                $value = $convertedDate->format(StdlibDateTime::DATETIME_PHP_FORMAT);
            }
        }

        return $value;
    }

    /**
     * Prepare Gift Card data
     *
     * @param GiftcardInterface $giftcard
     * @return GiftcardInterface
     */
    private function prepareGiftcard($giftcard)
    {
        $giftcard->setId(0);
        if (empty($giftcard->getDeliveryDate()) || empty($giftcard->getDeliveryDateTimezone())) {
            $giftcard
                ->setDeliveryDate(null)
                ->setDeliveryDateTimezone(null);
        }

        return $giftcard;
    }
}
