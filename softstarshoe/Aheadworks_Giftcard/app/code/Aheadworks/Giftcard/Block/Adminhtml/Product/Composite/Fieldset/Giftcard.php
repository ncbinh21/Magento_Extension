<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Block\Adminhtml\Product\Composite\Fieldset;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Email\Model\Template\Config as EmailConfig;
use Magento\Framework\Mail\Template\FactoryInterface as TemplateFactory;
use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Catalog\Block\Product\Context as ProductContext;
use Magento\Framework\Url\EncoderInterface as UrlEncoderInterface;
use Magento\Framework\Json\EncoderInterface as JsonEncoderInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\ProductTypes\ConfigInterface as ProductTypesConfigInterface;
use Magento\Framework\Locale\FormatInterface as LocaleFormatInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Config\Model\Config\Source\Locale\Timezone as TimezoneSource;

/**
 * Class Giftcard
 *
 * @package Aheadworks\Giftcard\Block\Adminhtml\Product\Composite\Fieldset
 */
class Giftcard extends \Aheadworks\Giftcard\Block\Product\View
{
    /**
     * @var AuthSession
     */
    private $authSession;

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
     * @param AuthSession $authSession
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
        AuthSession $authSession,
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
            $httpContext,
            $templateFactory,
            $emailConfig,
            $mediaConfig,
            $timezoneSource,
            $data
        );
        $this->authSession = $authSession;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderNameValue()
    {
        $senderName = parent::getSenderNameValue();
        if (!$senderName) {
            $senderName = $this->authSession->getUser()->getFirstname();
        }
        return $senderName;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderEmailValue()
    {
        $senderEmail = parent::getSenderEmailValue();
        if (!$senderEmail) {
            $senderEmail = $this->authSession->getUser()->getEmail();
        }
        return $senderEmail;
    }
}
