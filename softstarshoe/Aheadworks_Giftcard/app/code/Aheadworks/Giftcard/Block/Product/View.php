<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Block\Product;

use Aheadworks\Giftcard\Api\Data\OptionInterface;
use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardType;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Email\Model\Template\Config as EmailConfig;
use Magento\Framework\Mail\Template\FactoryInterface as TemplateFactory;
use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Catalog\Block\Product\Context as ProductContext;
use Magento\Framework\Url\EncoderInterface as UrlEncoderInterface;
use Magento\Framework\Json\EncoderInterface as JsonEncoderInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\ProductTypes\ConfigInterface as ProductTypesConfigInterface;
use Magento\Framework\Locale\FormatInterface as LocaleFormatInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;
use Magento\Config\Model\Config\Source\Locale\Timezone as TimezoneSource;

/**
 * Class View
 *
 * @package Aheadworks\Giftcard\Block\Product
 */
class View extends \Magento\Catalog\Block\Product\View
{
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var EmailConfig
     */
    private $emailConfig;

    /**
     * @var MediaConfig
     */
    private $mediaConfig;

    /**
     * @var TimezoneSource
     */
    private $timezoneSource;

    /**
     * @param ProductContext $context
     * @param UrlEncoderInterface $urlEncoder
     * @param JsonEncoderInterface $jsonEncoder
     * @param StringUtils $string
     * @param ProductHelper $productHelper
     * @param ProductTypesConfigInterface $productTypeConfig
     * @param LocaleFormatInterface $localeFormat
     * @param CustomerSession $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param HttpContext $httpContext
     * @param TemplateFactory $templateFactory
     * @param EmailConfig $emailConfig
     * @param MediaConfig $mediaConfig
     * @param TimezoneSource $timezoneSource
     * @param array $data
     */
    public function __construct(
        ProductContext $context,
        UrlEncoderInterface $urlEncoder,
        JsonEncoderInterface $jsonEncoder,
        StringUtils $string,
        ProductHelper $productHelper,
        ProductTypesConfigInterface $productTypeConfig,
        LocaleFormatInterface $localeFormat,
        CustomerSession $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        HttpContext $httpContext,
        TemplateFactory $templateFactory,
        EmailConfig $emailConfig,
        MediaConfig $mediaConfig,
        TimezoneSource $timezoneSource,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
        $this->httpContext = $httpContext;
        $this->templateFactory = $templateFactory;
        $this->emailConfig = $emailConfig;
        $this->mediaConfig = $mediaConfig;
        $this->timezoneSource = $timezoneSource;
    }

    /**
     * Retrieve fixed amount
     *
     * @return mixed
     */
    public function getFixedAmount()
    {
        $amountOptions = $this->getAmountOptions();
        return array_shift($amountOptions);
    }

    /**
     * Check is allow open amount
     *
     * @return bool
     */
    public function isAllowOpenAmount()
    {
        return (bool)$this->getProduct()->getData(ProductAttributeInterface::CODE_AW_GC_ALLOW_OPEN_AMOUNT);
    }

    /**
     * Retrieve currency symbol
     *
     * @return string
     */
    public function getDisplayCurrencySymbol()
    {
        return $this->priceCurrency->getCurrencySymbol();
    }

    /**
     * Check can render options
     *
     * @return bool
     */
    public function canRenderOptions()
    {
        return ($this->getProduct()->isSaleable() &&
            ($this->isAllowOpenAmount() || count($this->getAmountOptions()) > 0));
    }

    /**
     * Check is fixed amount
     *
     * @return bool
     */
    public function isFixedAmount()
    {
        return (count($this->getAmountOptions()) == 1) && !$this->isAllowOpenAmount();
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return (bool)$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * Check whether to display customer data or not
     *
     * @return bool
     */
    public function isDisplayCustomerData()
    {
        return $this->isCustomerLoggedIn() && !($this->_request->getModuleName() == 'checkout'
            && $this->_request->getControllerName() == 'cart' && $this->_request->getActionName() == 'configure');
    }

    /**
     * Retrieve Gift Card amounts
     *
     * @return []
     */
    public function getGiftcardAmounts()
    {
        $result = [
            [
                'value' => '',
                'label' => __('Choose an Amount...')
            ]
        ];
        $amountOptions = $this->getAmountOptions();
        foreach ($amountOptions as $option) {
            $result[] = [
                'value' => $option,
                'label' => $this->priceCurrency->convertAndFormat($option, false)
            ];
        }
        if ($this->isAllowOpenAmount()) {
            $result[] = [
                'value' => 'custom',
                'label' => __('Other Amount...')
            ];
        }
        return $result;
    }

    /**
     * Retrieve amount options
     *
     * @return mixed
     */
    public function getAmountOptions()
    {
        return $this->getProduct()->getTypeInstance()->getAmountOptions($this->getProduct());
    }

    /**
     * Retrieve min custom amount
     *
     * @return int
     */
    public function getMinCustomAmount()
    {
        if ($this->isAllowOpenAmount()) {
            $value = $this->getProduct()->getData(ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MIN);
            return $this->priceCurrency->convertAndRound($value);
        }
        return 0;
    }

    /**
     * Retrieve max custom amount
     *
     * @return int
     */
    public function getMaxCustomAmount()
    {
        if ($this->isAllowOpenAmount()) {
            $value = $this->getProduct()->getData(ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MAX);
            return $this->priceCurrency->convertAndRound($value);
        }
        return 0;
    }

    /**
     * Check is allow design select
     *
     * @return bool
     */
    public function isAllowDesignSelect()
    {
        return
            !($this->getProduct()->getData(ProductAttributeInterface::CODE_AW_GC_TYPE) == GiftcardType::VALUE_PHYSICAL)
            && !$this->isSingleDesign();
    }

    /**
     * Check is allow delivery date
     *
     * @return bool
     */
    public function isAllowDeliveryDate()
    {
        return (bool)$this->getProduct()->getData(ProductAttributeInterface::CODE_AW_GC_ALLOW_DELIVERY_DATE);
    }

    /**
     * Retrieve days between order and delivery dates
     *
     * @return int
     */
    public function getDaysOrderBetweenDelivery()
    {
        return (int)$this->getProduct()->getData(ProductAttributeInterface::CODE_AW_GC_DAYS_ORDER_DELIVERY);
    }

    /**
     * Retrieve Gift Card templates
     *
     * @return []
     */
    public function getGiftcardTemplates()
    {
        $result = [];
        $templateOptions = $this->getTemplateOptions();
        foreach ($templateOptions as $option) {
            $result[] = [
                'value' => $option['template'],
                'name' => $this->getTemplateName($option['template']),
                'imageUrl' => $option['image'] ? $this->mediaConfig->getTmpMediaUrl($option['image']) : ''
            ];
        }
        return $result;
    }

    /**
     * Check is single design
     *
     * @return bool
     */
    public function isSingleDesign()
    {
        return count($this->getTemplateOptions($this->getProduct())) == 1;
    }

    /**
     * Retrieve first template value
     *
     * @return mixed
     */
    public function getTemplateValue()
    {
        $options = $this->getTemplateOptions($this->getProduct());
        return $options[0]['template'];
    }

    /**
     * Check is allowed email
     *
     * @return bool
     */
    public function isAllowEmail()
    {
        return
            !($this->getProduct()->getData(ProductAttributeInterface::CODE_AW_GC_TYPE) == GiftcardType::VALUE_PHYSICAL);
    }

    /**
     * Check is allow headline
     *
     * @return bool
     */
    public function isAllowHeadline()
    {
        return $this->getProduct()->getTypeInstance()->headlineIsAllowed(
            $this->getProduct()->getData(ProductAttributeInterface::CODE_AW_GC_CUSTOM_MESSAGE_FIELDS)
        );
    }

    /**
     * Check is allow message
     *
     * @return bool
     */
    public function isAllowMessage()
    {
        return $this->getProduct()->getTypeInstance()->messageIsAllowed(
            $this->getProduct()->getData(ProductAttributeInterface::CODE_AW_GC_CUSTOM_MESSAGE_FIELDS)
        );
    }

    /**
     * Check is allow preview
     *
     * @return bool
     */
    public function isAllowPreview()
    {
        return
            !($this->getProduct()->getData(ProductAttributeInterface::CODE_AW_GC_TYPE) == GiftcardType::VALUE_PHYSICAL);
    }

    /**
     * Retrieve preview url
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->_urlBuilder->getUrl(
            'awgiftcard/product/preview',
            [
                'store' => $this->getProduct()->getStoreId(),
                '_secure' => $this->getRequest()->isSecure()
            ]
        );
    }

    /**
     * Retrieve timezones
     *
     * @return string[]
     */
    public function getTimezones()
    {
        return $this->timezoneSource->toOptionArray();
    }

    /**
     * Retrieve description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getProduct()->getData(ProductAttributeInterface::CODE_AW_GC_DESCRIPTION);
    }

    /**
     * Retrieve template options
     *
     * @return []
     */
    public function getTemplateOptions()
    {
        return $this->getProduct()->getTypeInstance()->getTemplateOptions($this->getProduct());
    }

    /**
     * Retrieves custom amount
     *
     * @return string
     */
    public function getAmountOptionValue()
    {
        return $this->getPreconfiguredOptionValue(OptionInterface::AMOUNT);
    }

    /**
     * Retrieves custom amount
     *
     * @return string
     */
    public function getCustomAmountOptionValue()
    {
        return $this->getPreconfiguredOptionValue(OptionInterface::CUSTOM_AMOUNT);
    }

    /**
     * Retrieves custom amount
     *
     * @return string
     */
    public function getTemplateOptionValue()
    {
        return $this->getPreconfiguredOptionValue(OptionInterface::TEMPLATE, 0);
    }

    /**
     * Retrieves delivery date
     *
     * @return string
     */
    public function getDeliveryDateValue()
    {
        return $this->getPreconfiguredOptionValue(OptionInterface::DELIVERY_DATE);
    }

    /**
     * Retrieves delivery date timezone
     *
     * @return string
     */
    public function getDeliveryDateTimezoneValue()
    {
        return $this->getPreconfiguredOptionValue(OptionInterface::DELIVERY_DATE_TIMEZONE);
    }

    /**
     * Retrieves sender name
     *
     * @return string
     */
    public function getSenderNameValue()
    {
        return $this->getPreconfiguredOptionValue(OptionInterface::SENDER_NAME);
    }

    /**
     * Retrieves sender email
     *
     * @return string
     */
    public function getSenderEmailValue()
    {
        return $this->getPreconfiguredOptionValue(OptionInterface::SENDER_EMAIL);
    }

    /**
     * Retrieves recipient name
     *
     * @return string
     */
    public function getRecipientNameValue()
    {
        return $this->getPreconfiguredOptionValue(OptionInterface::RECIPIENT_NAME);
    }

    /**
     * Retrieves recipient email
     *
     * @return string
     */
    public function getRecipientEmailValue()
    {
        return $this->getPreconfiguredOptionValue(OptionInterface::RECIPIENT_EMAIL);
    }

    /**
     * Retrieves headline
     *
     * @return string
     */
    public function getHeadlineValue()
    {
        return $this->getPreconfiguredOptionValue(OptionInterface::HEADLINE);
    }

    /**
     * Retrieves message
     *
     * @return string
     */
    public function getMessageValue()
    {
        return $this->getPreconfiguredOptionValue(OptionInterface::MESSAGE);
    }

    /**
     * Retrieve template name
     *
     * @param int|string $templateId
     * @return string
     */
    private function getTemplateName($templateId)
    {
        /** @var \Magento\Email\Model\Template $template */
        $template = $this->templateFactory->get($templateId);
        if (is_numeric($templateId)) {
            return $template->load($templateId)->getTemplateCode();
        } else {
            return $this->emailConfig->getTemplateLabel($templateId);
        }
    }

    /**
     * Retrieves preconfigured option value by code
     *
     * @param string $code
     * @param string $default
     * @return mixed|string
     */
    private function getPreconfiguredOptionValue($code, $default = '')
    {
        $product = $this->getProduct();
        $value = $product->getPreconfiguredValues()->getData($code);
        return $value ? $value : $default;
    }
}
