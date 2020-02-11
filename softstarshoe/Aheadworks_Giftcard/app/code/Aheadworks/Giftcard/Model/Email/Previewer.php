<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Email;

use Aheadworks\Giftcard\Api\Data\OptionInterface;
use Aheadworks\Giftcard\Api\Data\OptionInterfaceFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Area as AppArea;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\TemplateInterface;
use Aheadworks\Giftcard\Model\Email\Variables\CardImageBaseUrl\Render as CardImageBaseUrlRender;

/**
 * Class Previewer
 *
 * @package Aheadworks\Giftcard\Model\Email
 */
class Previewer
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CardImageBaseUrlRender
     */
    private $cardImageBaseUrlRender;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var OptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * @var FactoryInterface
     */
    private $templateFactory;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreManagerInterface $storeManager
     * @param CardImageBaseUrlRender $cardImageBaseUrlRender
     * @param DataObjectHelper $dataObjectHelper
     * @param OptionInterfaceFactory $optionFactory
     * @param FactoryInterface $templateFactory
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
        CardImageBaseUrlRender $cardImageBaseUrlRender,
        DataObjectHelper $dataObjectHelper,
        OptionInterfaceFactory $optionFactory,
        FactoryInterface $templateFactory
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->cardImageBaseUrlRender = $cardImageBaseUrlRender;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->optionFactory = $optionFactory;
        $this->templateFactory = $templateFactory;
    }

    /**
     * Generate preview template
     *
     * @param string[] $customOptions
     * @param int $storeId
     * @param int $productId
     * @return string
     */
    public function getPreview($customOptions, $storeId, $productId)
    {
        /** @var OptionInterface $optionObject */
        $optionObject = $this->optionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $optionObject,
            $customOptions,
            OptionInterface::class
        );

        $templateVars = $this->prepare($optionObject, $storeId, $productId);
        $template = $this->getTemplate(
            $optionObject->getAwGcTemplate(),
            [
                'area' => AppArea::AREA_FRONTEND,
                'store' => $storeId
            ],
            $templateVars
        );
        return $template->processTemplate();
    }

    /**
     * Prepare data for preview
     *
     * @param OptionInterface $optionObject
     * @param int $storeId
     * @param int $productId
     * @return string[]
     */
    private function prepare($optionObject, $storeId, $productId)
    {
        $templateVars['recipient_name'] = $optionObject->getAwGcRecipientName();
        $templateVars['recipient_email'] = $optionObject->getAwGcRecipientEmail();
        $templateVars['sender_name'] = $optionObject->getAwGcSenderName();
        $templateVars['sender_email'] = $optionObject->getAwGcSenderEmail();
        $templateVars['headline'] = $optionObject->getAwGcHeadline();
        $templateVars['message'] = $optionObject->getAwGcMessage();

        /** @var StoreInterface $store */
        $store = $this->storeManager->getStore($storeId);
        $balance = $optionObject->getAwGcAmount() == 'custom'
            ? $optionObject->getAwGcCustomAmount()
            : $optionObject->getAwGcAmount();
        $templateVars['giftcards'] = ['XXXXXXXXXXXX'];
        $templateVars['is_multiple_codes'] = false;
        $templateVars['balance'] = $this->priceCurrency->convertAndFormat(
            $balance,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $store->getId()
        );
        $templateVars['card_image_base_url'] = $this->cardImageBaseUrlRender
            ->render($storeId, $productId, $optionObject->getAwGcTemplate());

        return $templateVars;
    }

    /**
     * Retrieve template by id
     *
     * @param string $templateId
     * @param [] $templateOptions
     * @param [] $templateVars
     * @return TemplateInterface
     */
    private function getTemplate($templateId, $templateOptions, $templateVars)
    {
        return $this->templateFactory->get($templateId)
            ->setVars($templateVars)
            ->setOptions($templateOptions);
    }
}
