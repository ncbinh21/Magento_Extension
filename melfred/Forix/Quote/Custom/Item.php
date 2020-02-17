<?php

namespace Forix\Quote\Custom;

use Forix\Base\Helper\Data;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;

class Item extends  \Magento\Quote\Model\Quote\Item
{
    /**
     * @var \Magento\NegotiableQuote\Model\NegotiableQuoteRepository
     */
    protected $negotiableQuoteRepository;

    /**
     * Item constructor.
     * @param \Magento\NegotiableQuote\Api\NegotiableQuoteItemRepositoryInterface $negotiableQuoteItemRepository
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Sales\Model\Status\ListFactory $statusListFactory
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Quote\Model\Quote\Item\OptionFactory $itemOptionFactory
     * @param \Magento\Quote\Model\Quote\Item\Compare $quoteItemCompare
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Magento\NegotiableQuote\Model\NegotiableQuoteRepository $negotiableQuoteRepository,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Model\Status\ListFactory $statusListFactory,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Quote\Model\Quote\Item\OptionFactory $itemOptionFactory,
        \Magento\Quote\Model\Quote\Item\Compare $quoteItemCompare,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->negotiableQuoteRepository = $negotiableQuoteRepository;
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $productRepository, $priceCurrency, $statusListFactory, $localeFormat, $itemOptionFactory, $quoteItemCompare, $stockRegistry, $resource, $resourceCollection, $data, $serializer);
    }

    /**
     * @param $quoteId
     * @return string
     */
    public function getNegotiableQuotePrice($quoteId)
    {
        $negotiableQuote = $this->negotiableQuoteRepository->getById($quoteId);
        return $negotiableQuote->getExpirationPeriod();
    }

    /**
     * @return $this
     */
    public function saveItemOptions()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$helper = $objectManager->get('Forix\Base\Helper\Data');
		$fullActionName = $helper->getFullActionName();
		if ($fullActionName == "requisition_list_item_addToCart") {
			foreach ($this->_options as $index => $option) {
				if ($option->isDeleted()) {
					$option->delete();
					unset($this->_options[$index]);
					unset($this->_optionsByCode[$option->getCode()]);
				} else {
					if (!$option->getItem() || !$option->getItem()->getId()) {
						$option->setItem($this);
					}
					$option->save();
				}
			}
			return $this;
		}
		return parent::saveItemOptions();

	}
}