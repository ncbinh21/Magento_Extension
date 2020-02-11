<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Email;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Model\Config;
use Aheadworks\Giftcard\Model\Source\EmailStatus;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Giftcard\Model\Email\Variables\CardImageBaseUrl\Render as CardImageBaseUrlRender;

/**
 * Class Sender
 *
 * @package Aheadworks\Giftcard\Model\Email
 */
class Sender
{
    /**
     * ID of default Gift Card email template
     */
    const DEFAULT_EMAIL_TEMPLATE_ID = 'aw_giftcard_email_template';

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var AppEmulation
     */
    private $appEmulation;

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
     * @param TransportBuilder $transportBuilder
     * @param Config $config
     * @param TimezoneInterface $localeDate
     * @param AppEmulation $appEmulation
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreManagerInterface $storeManager
     * @param CardImageBaseUrlRender $cardImageBaseUrlRender
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        Config $config,
        TimezoneInterface $localeDate,
        AppEmulation $appEmulation,
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
        CardImageBaseUrlRender $cardImageBaseUrlRender
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->config = $config;
        $this->localeDate = $localeDate;
        $this->appEmulation = $appEmulation;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->cardImageBaseUrlRender = $cardImageBaseUrlRender;
    }

    /**
     * Send email using given Gift Card codes.
     * Do not use this method to send non-processed Gift Cards.
     * To send Gift Card codes, use GiftcardManagementInterface and sendGiftcardByCode() method
     *
     * @param GiftcardInterface|GiftcardInterface[] $giftcards
     * @param int|null $storeId
     * @return int
     */
    public function sendGiftcards($giftcards, $storeId = null)
    {
        if (!is_array($giftcards)) {
            $giftcards = [$giftcards];
        }
        /** @var GiftcardInterface $giftcard */
        $giftcard = $giftcards[0];
        $template = $giftcard->getEmailTemplate();
        $recipientName = $giftcard->getRecipientName();
        $recipientEmail = $giftcard->getRecipientEmail();
        if (!$storeId) {
            $storeId = $this->storeManager->getWebsite($giftcard->getWebsiteId())->getDefaultStore()->getId();
        }
        /** @var StoreInterface $store */
        $store = $this->storeManager->getStore($storeId);
        $senderEmail = $this->config->getEmailSender($store->getId());

        $sendStatus = $this->send(
            $template,
            [
                'area' => Area::AREA_FRONTEND,
                'store' => $store->getId()
            ],
            $this->prepareTemplateVars(
                [
                    'store' => $store,
                    'giftcards' => $giftcards
                ]
            ),
            $senderEmail,
            [$recipientName => $recipientEmail]
        );
        return $sendStatus;
    }

    /**
     * Send email
     *
     * @param string $templateId
     * @param array $templateOptions
     * @param array $templateVars
     * @param string $from
     * @param array $to
     * @return int
     */
    private function send($templateId, $templateOptions, $templateVars, $from, $to)
    {
        try {
            $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($to);
            $this->transportBuilder->getTransport()->sendMessage();
        } catch (\Exception $e) {
            return EmailStatus::FAILED;
        }
        return EmailStatus::SENT;
    }

    /**
     * Prepare template vars
     *
     * @param [] $data
     * @return []
     */
    private function prepareTemplateVars($data)
    {
        /** @var StoreInterface $store */
        $store = $data['store'];
        $giftcards = $data['giftcards'];
        $templateVars = [
            'store' => $store,
            'store_name' => $store->getName()
        ];

        $giftcardCodes = [];
        $balance = 0;
        $expiredAt = false;
        /** @var GiftcardInterface $giftcard */
        foreach ($giftcards as $giftcard) {
            if ($giftcard->getRecipientName()) {
                $templateVars['recipient_name'] = $giftcard->getRecipientName();
            }
            if ($giftcard->getRecipientEmail()) {
                $templateVars['recipient_email'] = $giftcard->getRecipientEmail();
            }
            if ($giftcard->getSenderName()) {
                $templateVars['sender_name'] = $giftcard->getSenderName();
            }
            if ($giftcard->getSenderEmail()) {
                $templateVars['sender_email'] = $giftcard->getSenderEmail();
            }
            if ($giftcard->getHeadline()) {
                $templateVars['headline'] = $giftcard->getHeadline();
            }
            if ($giftcard->getMessage()) {
                $templateVars['message'] = $giftcard->getMessage();
            }
            $giftcardCodes[] = $giftcard->getCode();
            $balance += $giftcard->getBalance();
        }

        $templateVars['giftcards'] = $giftcardCodes;
        $templateVars['is_multiple_codes'] = (bool)(count($giftcardCodes) > 1);
        $templateVars['balance'] = $this->priceCurrency->convertAndFormat(
            $balance,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $store->getId()
        );

        if ($expiredAt) {
            $templateVars['expired_at'] = $this->localeDate
                ->scopeDate($store, $data['expire_date'], true)
                ->format('d M Y');
        }

        $this->appEmulation->startEnvironmentEmulation($store->getId(), Area::AREA_FRONTEND, true);
        $templateVars['card_image_base_url'] = $this->cardImageBaseUrlRender
            ->render($store->getId(), $giftcard->getProductId(), $giftcard->getEmailTemplate());
        $this->appEmulation->stopEnvironmentEmulation();

        return $templateVars;
    }
}
