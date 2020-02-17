<?php

namespace Forix\Quote\Rewrite\Ui\Component\Listing\Column;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\NegotiableQuote\Api\Data\NegotiableQuoteInterface;

class Price extends \Magento\NegotiableQuote\Ui\Component\Listing\Column\Price
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    private $userContext;

    /**
     * Price constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PriceCurrencyInterface $priceFormatter
     * @param StoreManagerInterface $storeManager
     * @param UserContextInterface $userContext
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PriceCurrencyInterface $priceFormatter,
        StoreManagerInterface $storeManager,
        UserContextInterface $userContext,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        array $components = [],
        array $data = []
    ) {
        $this->userContext = $userContext;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
        parent::__construct($context, $uiComponentFactory, $priceFormatter, $storeManager, $userContext, $serializer, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $price = $item[$this->getData('name')];
                if ($this->isAuthor($item) && $this->isStatusNotBlocked($item) && $this->isCurrencyChange($item)) {
                    $price = $this->priceFormatter->convert($price);
                    $currencyCode = $this->priceFormatter->getCurrency()->getCode();
                } elseif (!$this->isStatusNotBlocked($item) && isset($item['snapshot'])) {
                    $snapshot = $this->serializer->unserialize($item['snapshot']);
                    $price = $snapshot['quote']['grand_total'];
                    $currencyCode = $snapshot['quote']['quote_currency_code'];
                } else {
                    $price = $item['grand_total'];
                    $currencyCode = $item['quote_currency_code'];
                }
                if (isset($item['expiration_period']) && $item['expiration_period']) {
                    $item[$this->getData('name')] = $this->priceFormatter->format(
                        $price,
                        false,
                        2,
                        null,
                        $currencyCode
                    );
                } else {
                    $item[$this->getData('name')] = null;
                }
            }
        }

        return $dataSource;
    }

    /**
     * Check is status of quote isn't close or ordered.
     *
     * @param array $item
     * @return bool
     */
    private function isStatusNotBlocked(array $item)
    {
        $blockedStatuses = [NegotiableQuoteInterface::STATUS_CLOSED, NegotiableQuoteInterface::STATUS_ORDERED];
        $status = isset($item['status_original']) ? $item['status_original'] : $item['status'];
        return !in_array($status, $blockedStatuses);
    }

    /**
     * Check is current user is author of quote.
     *
     * @param array $item
     * @return bool
     */
    private function isAuthor(array $item)
    {
        $userId = isset($item['customer_id_original']) ? $item['customer_id_original'] : $item['customer_id'];
        return $userId == $this->userContext->getUserId();
    }

    /**
     * Check currency in quote and store.
     *
     * @param array $item
     * @return bool
     */
    private function isCurrencyChange(array $item)
    {
        $store = $this->storeManager->getStore($item['store_id']);
        return !isset($item['quote_currency_code'])
            || !isset($item['base_currency_code'])
            || $item['quote_currency_code'] != $store->getCurrentCurrency()->getCode()
            || $item['base_currency_code'] != $store->getBaseCurrency()->getCode()
            || $item['base_to_quote_rate'] != $store->getBaseCurrency()->getRate($item['quote_currency_code']);
    }
}