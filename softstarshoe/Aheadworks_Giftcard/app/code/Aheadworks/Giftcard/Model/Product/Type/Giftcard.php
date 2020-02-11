<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Product\Type;

use Aheadworks\Giftcard\Api\Data\OptionInterface;
use Aheadworks\Giftcard\Api\Data\OptionInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;
use Aheadworks\Giftcard\Model\Product\Option;
use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardCustomMessage;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardType;
use Aheadworks\Giftcard\Model\Email\Sender;
use Magento\Framework\DataObject;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Aheadworks\Giftcard\Model\Statistics;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Catalog\Model\Product\Option as CatalogProductOption;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Model\Product\Type as CatalogProductType;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\MediaStorage\Helper\File\Storage\Database as FileStorageDatabase;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Mail\Template\FactoryInterface as MailTemplateFactoryInterface;
use Magento\Email\Model\Template\Config as EmailTemplateConfig;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Giftcard
 *
 * @package Aheadworks\Giftcard\Model\Product\Type
 */
class Giftcard extends AbstractType
{
    /**
     * Gift Card product type code
     */
    const TYPE_CODE = 'aw_giftcard';

    /**
     * @var Statistics
     */
    private $statistics;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var MailTemplateFactoryInterface
     */
    private $emailTemplateFactory;

    /**
     * @var EmailTemplateConfig
     */
    private $emailTemplateConfig;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var OptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * If product can be configured
     *
     * @var bool
     */
    protected $_canConfigure = true;

    /**
     * @param CatalogProductOption $catalogProductOption
     * @param EavConfig $eavConfig
     * @param CatalogProductType $catalogProductType
     * @param EventManagerInterface $eventManager
     * @param FileStorageDatabase $fileStorageDb
     * @param Filesystem $filesystem
     * @param Registry $coreRegistry
     * @param LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param Statistics $statistics
     * @param CustomerSession $customerSession
     * @param MailTemplateFactoryInterface $emailTemplateFactory
     * @param EmailTemplateConfig $emailTemplateConfig
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreManagerInterface $storeManager
     * @param DataObjectHelper $dataObjectHelper
     * @param OptionInterfaceFactory $optionFactory
     * @param TimezoneInterface $localeDate
     * @param RequestInterface $request
     */
    public function __construct(
        CatalogProductOption $catalogProductOption,
        EavConfig $eavConfig,
        CatalogProductType $catalogProductType,
        EventManagerInterface $eventManager,
        FileStorageDatabase $fileStorageDb,
        Filesystem $filesystem,
        Registry $coreRegistry,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        Statistics $statistics,
        CustomerSession $customerSession,
        MailTemplateFactoryInterface $emailTemplateFactory,
        EmailTemplateConfig $emailTemplateConfig,
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
        DataObjectHelper $dataObjectHelper,
        OptionInterfaceFactory $optionFactory,
        TimezoneInterface $localeDate,
        RequestInterface $request
    ) {
        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository
        );
        $this->statistics = $statistics;
        $this->customerSession = $customerSession;
        $this->emailTemplateFactory = $emailTemplateFactory;
        $this->emailTemplateConfig = $emailTemplateConfig;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->optionFactory = $optionFactory;
        $this->localeDate = $localeDate;
        $this->request = $request;
    }

    /**
     * Check is virtual type
     *
     * @param Product $product
     * @return bool
     */
    public function isTypeVirtual(Product $product)
    {
        return
            $this->getAttribute($product, ProductAttributeInterface::CODE_AW_GC_TYPE) == GiftcardType::VALUE_VIRTUAL;
    }

    /**
     * Check is physical type
     *
     * @param Product $product
     * @return bool
     */
    public function isTypePhysical(Product $product)
    {
        return
            $this->getAttribute($product, ProductAttributeInterface::CODE_AW_GC_TYPE) == GiftcardType::VALUE_PHYSICAL;
    }

    /**
     * Check is combined type
     *
     * @param Product $product
     * @return bool
     */
    public function isTypeCombined(Product $product)
    {
        return
            $this->getAttribute($product, ProductAttributeInterface::CODE_AW_GC_TYPE) == GiftcardType::VALUE_COMBINED;
    }

    /**
     * Retrieves amounts of given Gift Card product
     *
     * @param Product $product
     * @return []
     */
    public function getAmounts(Product $product)
    {
        $amounts = [];
        $websiteId = $product->getStore()->getWebsiteId();
        $amountsData = $this->getAttribute($product, ProductAttributeInterface::CODE_AW_GC_AMOUNTS);
        foreach ($amountsData as $data) {
            if (in_array($data['website_id'], [$websiteId, 0])) {
                $amounts[] = $data['price'];
            }
        }
        return $amounts;
    }

    /**
     * Get amount options
     *
     * @param Product $product
     * @return array
     */
    public function getAmountOptions(Product $product)
    {
        $amountOptions = $this->getAmounts($product);
        sort($amountOptions);
        return $amountOptions;
    }

    /**
     * Get email templates options
     *
     * @param Product $product
     * @return array
     */
    public function getTemplateOptions(Product $product)
    {
        $templateOptions = [];
        $storeId = $product->getStoreId();
        $templatesData = $product->getData(ProductAttributeInterface::CODE_AW_GC_EMAIL_TEMPLATES);
        foreach ($templatesData as $data) {
            if (in_array($data['store_id'], [$storeId, 0])) {
                $templateOptions[] = [
                    'template' => $data['template'],
                    'image' => $data['image']
                ];
            }
        }
        return $templateOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function isVirtual($product)
    {
        return $this->isTypeVirtual($product) ? : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderOptions($product)
    {
        $customOptions = [];
        /** @var \Magento\Quote\Model\Quote\Item\Option $option */
        foreach ($product->getCustomOptions() as $option) {
            $customOptions[$option->getCode()] = $option->getValue();
        }

        /** @var Option $optionObject */
        $optionObject = $this->optionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $optionObject,
            $customOptions,
            OptionInterface::class
        );

        $resultValidDeliveryDate = $this->isValidDeliveryDate(
            $optionObject->getAwGcDeliveryDate(),
            $optionObject->getAwGcDeliveryDateTimezone()
        );
        if (!$resultValidDeliveryDate['success']) {
            $optionObject->setAwGcDeliveryDate(null);
            $optionObject->setAwGcDeliveryDateTimezone(null);
        }
        $options = $optionObject->getData();
        $info = $product->getCustomOption('info_buyRequest');
        if ($info) {
            if (property_exists($this, 'serializer')) {
                $options['info_buyRequest'] = $this->serializer->unserialize($info->getValue());
            } else {
                $options['info_buyRequest'] = unserialize($info->getValue());
            }
            if (!$resultValidDeliveryDate['success']
                && isset($options['info_buyRequest'][OptionInterface::DELIVERY_DATE])
            ) {
                $options['info_buyRequest'][OptionInterface::DELIVERY_DATE] = '';
            }
        }
        return $options;
    }

    /**
     * Prepare selected options for product
     *
     * @param Product $product
     * @param DataObject $buyRequest
     * @return []
     */
    public function processBuyRequest($product, $buyRequest)
    {
        /** @var Option $optionObject */
        $optionObject = $this->optionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $optionObject,
            $buyRequest->getData(),
            OptionInterface::class
        );
        $resultValidDeliveryDate = $this->isValidDeliveryDate(
            $optionObject->getAwGcDeliveryDate(),
            $optionObject->getAwGcDeliveryDateTimezone()
        );
        if (!$resultValidDeliveryDate['success']) {
            $optionObject->setAwGcDeliveryDate(null);
            $optionObject->setAwGcDeliveryDateTimezone(null);
        }
        return $optionObject->getData();
    }

    /**
     * Delete data specific for this product type
     *
     * @param Product $product
     * @return void
     */
    public function deleteTypeSpecificData(Product $product)
    {
    }

    /**
     * Save type related data
     *
     * @param Product $product
     * @return $this
     */
    public function save($product)
    {
        foreach ($product->getStoreIds() as $storeId) {
            $this->statistics->createStatistics($product->getId(), $storeId);
        }
        return parent::save($product);
    }

    /**
     * Check is allowed headline or not
     *
     * @param int $state
     * @return bool
     */
    public function headlineIsAllowed($state)
    {
        $allowedState = [
            GiftcardCustomMessage::SHOW_HEADLINE_AND_MESSAGE,
            GiftcardCustomMessage::SHOW_HEADLINE_ONLY
        ];
        return in_array($state, $allowedState);
    }

    /**
     * Check is allowed message or not
     *
     * @param int $state
     * @return bool
     */
    public function messageIsAllowed($state)
    {
        $allowedState = [
            GiftcardCustomMessage::SHOW_HEADLINE_AND_MESSAGE,
            GiftcardCustomMessage::SHOW_MESSAGE_ONLY
        ];
        return in_array($state, $allowedState);
    }

    /**
     * Retrieve product attribute by code
     *
     * @param Product $product
     * @param string $code
     * @return mixed
     */
    public function getAttribute(Product $product, $code)
    {
        if (!$product->hasData($code)) {
            $product->getResource()->load($product, $product->getId());
        }
        return $product->getData($code);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareProduct(DataObject $buyRequest, $product, $processMode)
    {
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);
        if (is_string($result)) {
            return $result;
        }
        try {
            $this->validateBuyRequest($buyRequest, $product, $processMode);
            $amount = $this->getAmount($buyRequest, $product);
            $this->validateAmount($buyRequest, $product, $processMode, $amount);
        } catch (LocalizedException $e) {
            return $e->getMessage();
        } catch (\Exception $e) {
            return __('An error has occurred while adding product to cart');
        }
        $senderName = $buyRequest->getData(OptionInterface::SENDER_NAME);
        $recipientName = $buyRequest->getData(OptionInterface::RECIPIENT_NAME);

        $product->addCustomOption(OptionInterface::AMOUNT, $amount, $product);
        $product->addCustomOption(OptionInterface::SENDER_NAME, $senderName, $product);
        $product->addCustomOption(OptionInterface::RECIPIENT_NAME, $recipientName, $product);

        if (!$this->isTypePhysical($product)) {
            $senderEmail = $buyRequest->getData(OptionInterface::SENDER_EMAIL);
            $recipientEmail = $buyRequest->getData(OptionInterface::RECIPIENT_EMAIL);

            $product->addCustomOption(OptionInterface::SENDER_EMAIL, $senderEmail, $product);
            $product->addCustomOption(OptionInterface::RECIPIENT_EMAIL, $recipientEmail, $product);

            $emailTemplateId = $buyRequest->getData(OptionInterface::TEMPLATE);
            $emailTemplateName = null;
            if (null != $emailTemplateId) {
                /** @var \Magento\Email\Model\Template $emailTemplate */
                $emailTemplate = $this->emailTemplateFactory->get($emailTemplateId);
                if (is_numeric($emailTemplateId)) {
                    $emailTemplate->load($emailTemplateId);
                    if (!$emailTemplate->getId()) {
                        $emailTemplateId = Sender::DEFAULT_EMAIL_TEMPLATE_ID;
                    }
                } else {
                    $emailTemplate->loadDefault($emailTemplateId);
                }
                $emailTemplateName = is_numeric($emailTemplateId)
                    ? $emailTemplate->getTemplateCode()
                    : $this->emailTemplateConfig->getTemplateLabel($emailTemplateId)->getText();
            }
            $product->addCustomOption(OptionInterface::TEMPLATE, $emailTemplateId, $product);
            $product->addCustomOption(OptionInterface::TEMPLATE_NAME, $emailTemplateName, $product);
        }
        $headlineValue = null;
        $messageValue = null;
        if ($this->headlineIsAllowed($product->getData(ProductAttributeInterface::CODE_AW_GC_CUSTOM_MESSAGE_FIELDS))) {
            $headlineValue = $buyRequest->getData(OptionInterface::HEADLINE);
        }
        if ($this->messageIsAllowed($product->getData(ProductAttributeInterface::CODE_AW_GC_CUSTOM_MESSAGE_FIELDS))) {
            $messageValue = $buyRequest->getData(OptionInterface::MESSAGE);
        }
        $deliveryDateValue = null;
        $deliveryDateTimezoneValue = null;
        if ($product->getData(ProductAttributeInterface::CODE_AW_GC_ALLOW_DELIVERY_DATE)) {
            $deliveryDateValue = $buyRequest->getData(OptionInterface::DELIVERY_DATE);
            $deliveryDateTimezoneValue = $deliveryDateValue
                ? $buyRequest->getData(OptionInterface::DELIVERY_DATE_TIMEZONE)
                : '';
        }
        $giftcardType = $this->getAttribute($product, ProductAttributeInterface::CODE_AW_GC_TYPE);

        $product->addCustomOption(OptionInterface::HEADLINE, $headlineValue, $product);
        $product->addCustomOption(OptionInterface::MESSAGE, $messageValue, $product);
        $product->addCustomOption(OptionInterface::DELIVERY_DATE, $deliveryDateValue, $product);
        $product->addCustomOption(OptionInterface::DELIVERY_DATE_TIMEZONE, $deliveryDateTimezoneValue, $product);
        $product->addCustomOption(OptionInterface::GIFTCARD_TYPE, $giftcardType, $product);
        return $result;
    }

    /**
     * Validate buy request
     *
     * @param DataObject $buyRequest
     * @param Product $product
     * @param string $processMode
     * @return void
     * @throws LocalizedException
     */
    private function validateBuyRequest(DataObject $buyRequest, $product, $processMode)
    {
        if ($this->isCustomAmount($buyRequest, $product) && $buyRequest->getData(OptionInterface::CUSTOM_AMOUNT) <= 0
            && $this->_isStrictProcessMode($processMode)
        ) {
            throw new LocalizedException(__('Please specify Gift Card amount'));
        }
        if (!$buyRequest->getData(OptionInterface::RECIPIENT_NAME) && $this->_isStrictProcessMode($processMode)) {
            throw new LocalizedException(__('Please specify recipient name'));
        }
        if (!$buyRequest->getData(OptionInterface::SENDER_NAME) && !$this->customerSession->isLoggedIn()
            && $this->_isStrictProcessMode($processMode)
        ) {
            throw new LocalizedException(__('Please specify sender name'));
        }
        if (!$this->isTypePhysical($product)) {
            if (!$buyRequest->getData(OptionInterface::RECIPIENT_EMAIL) && $this->_isStrictProcessMode($processMode)) {
                throw new LocalizedException(__('Please specify recipient email'));
            }
            if (!$buyRequest->getData(OptionInterface::SENDER_EMAIL) && !$this->customerSession->isLoggedIn()
                && $this->_isStrictProcessMode($processMode)
            ) {
                throw new LocalizedException(__('Please specify sender email'));
            }
            if (!$buyRequest->getData(OptionInterface::TEMPLATE) && $this->_isStrictProcessMode($processMode)) {
                throw new LocalizedException(__('Please specify a design'));
            }
        }
        $resultValidDeliveryDate = $this->isValidDeliveryDate(
            $buyRequest->getData(OptionInterface::DELIVERY_DATE),
            $buyRequest->getData(OptionInterface::DELIVERY_DATE_TIMEZONE)
        );
        if (!$resultValidDeliveryDate['success'] && $this->_isStrictProcessMode($processMode)) {
            if ($this->request->getModuleName() == 'sales' && $this->request->getActionName() == 'reorder') {
                $buyRequest->setData(OptionInterface::DELIVERY_DATE, null);
                $buyRequest->setData(OptionInterface::DELIVERY_DATE_TIMEZONE, null);
            } else {
                throw new LocalizedException(__($resultValidDeliveryDate['message']));
            }
        }
    }

    /**
     * Check is valid delivery date
     *
     * @param string $deliveryDate
     * @param string $deliveryDateTimezone
     * @return []
     */
    private function isValidDeliveryDate($deliveryDate, $deliveryDateTimezone)
    {
        $result = [
            'success' => true,
            'message' => ''
        ];
        if (!$deliveryDate) {
            return $result;
        }
        $zendValidateArgs = ['format' => OptionInterface::DELIVERY_DATE_FORMAT_ON_STOREFRONT, 'locale' => 'en_US'];
        if (!\Zend_Validate::is($deliveryDate, 'Date', $zendValidateArgs)) {
            $result['success'] = false;
            $result['message'] = 'Delivery date is incorrect';
        } else {
            $currentDate = new \DateTime('now', new \DateTimeZone($deliveryDateTimezone));
            $currentDate->setTime(0, 0, 0);
            $deliverydate = new \DateTime($deliveryDate, new \DateTimeZone($deliveryDateTimezone));

            if ($deliverydate < $currentDate) {
                $result['success'] = false;
                $result['message'] = 'Start date must be in future';
            }
        }
        return $result;
    }

    /**
     * Check is custom amount
     *
     * @param DataObject $buyRequest
     * @param Product $product
     * @return bool
     */
    private function isCustomAmount(DataObject $buyRequest, $product)
    {
        return (
            $buyRequest->getData(OptionInterface::AMOUNT) == 'custom'
            || !$buyRequest->getData(OptionInterface::AMOUNT)
        ) && $product->getData(ProductAttributeInterface::CODE_AW_GC_ALLOW_OPEN_AMOUNT);
    }

    /**
     * Retrieve amount
     *
     * @param DataObject $buyRequest
     * @param Product $product
     * @return float|null|string
     */
    private function getAmount(DataObject $buyRequest, $product)
    {
        $amountOptions = $this->getAmountOptions($product);
        $selectedAmountOption = $buyRequest->getData(OptionInterface::AMOUNT);
        $customAmount = $buyRequest->getData(OptionInterface::CUSTOM_AMOUNT);

        $amount = null;
        if ($this->isCustomAmount($buyRequest, $product)) {
            /** @var \Magento\Directory\Model\Currency $currency */
            $currency = $this->priceCurrency->getCurrency($product->getStoreId());
            $baseCurrency = $this->storeManager->getStore($product->getStoreId())->getBaseCurrency();
            $amount = $currency->convert($customAmount, $baseCurrency);
        }
        if (is_numeric($selectedAmountOption) && in_array($selectedAmountOption, $amountOptions)) {
            $amount = $selectedAmountOption;
        }
        if (null === $amount && count($amountOptions) == 1) {
            $amount = array_shift($amountOptions);
        }
        return $amount;
    }

    /**
     * Validate amount
     *
     * @param DataObject $buyRequest
     * @param Product $product
     * @param string $processMode
     * @param string|float $amount
     * @return void
     * @throws LocalizedException
     */
    private function validateAmount(DataObject $buyRequest, $product, $processMode, $amount)
    {
        if (null === $amount && $this->_isStrictProcessMode($processMode)) {
            throw new LocalizedException(__('Please specify Gift Card amount'));
        }
        if ($this->isCustomAmount($buyRequest, $product)) {
            $minOpenAmount = $product->getData(ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MIN);
            $maxOpenAmount = $product->getData(ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MAX);

            if ($maxOpenAmount && $amount > $maxOpenAmount && $this->_isStrictProcessMode($processMode)) {
                $formattedMaxOpenAmount = $this->priceCurrency->convertAndFormat($maxOpenAmount, false);
                throw new LocalizedException(
                    __('Maximum allowed Gift Card amount is %1', $formattedMaxOpenAmount)
                );
            }
            if ($minOpenAmount && $amount < $minOpenAmount && $this->_isStrictProcessMode($processMode)) {
                $formattedMinOpenAmount = $this->priceCurrency->convertAndFormat($minOpenAmount, false);
                throw new LocalizedException(
                    __('Minimum allowed Gift Card amount is %1', $formattedMinOpenAmount)
                );
            }
        }
    }
}
