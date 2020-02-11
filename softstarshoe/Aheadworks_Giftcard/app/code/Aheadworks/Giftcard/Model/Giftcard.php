<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Api\GiftcardManagementInterface;
use Aheadworks\Giftcard\Model\Source\EmailStatus;
use Aheadworks\Giftcard\Model\Source\Giftcard\EmailTemplate;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard as ResourceGiftcard;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardType;
use Aheadworks\Giftcard\Model\Source\Giftcard\Status;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Giftcard
 *
 * @package Aheadworks\Giftcard\Model
 */
class Giftcard extends AbstractModel implements GiftcardInterface
{
    /**
     * @var GiftcardManagementInterface
     */
    private $giftcardManagement;

    /**
     * @var Statistics
     */
    private $statistics;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param GiftcardManagementInterface $giftcardManagement
     * @param Statistics $statistics
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        Context $context,
        Registry $registry,
        GiftcardManagementInterface $giftcardManagement,
        Statistics $statistics,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate
    ) {
        $this->giftcardManagement = $giftcardManagement;
        $this->statistics = $statistics;
        $this->storeManager = $storeManager;
        $this->localeDate = $localeDate;
        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceGiftcard::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpireAt()
    {
        return $this->getData(self::EXPIRE_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpireAt($expireAt)
    {
        return $this->setData(self::EXPIRE_AT, $expireAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * {@inheritdoc}
     */
    public function getInitialBalance()
    {
        return $this->getData(self::INITIAL_BALANCE);
    }

    /**
     * {@inheritdoc}
     */
    public function setInitialBalance($initialBalance)
    {
        return $this->setData(self::INITIAL_BALANCE, $initialBalance);
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->getData(self::STATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setState($state)
    {
        return $this->setData(self::STATE, $state);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailTemplate()
    {
        return $this->getData(self::EMAIL_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailTemplate($emailTemplate)
    {
        return $this->setData(self::EMAIL_TEMPLATE, $emailTemplate);
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderName()
    {
        return $this->getData(self::SENDER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderName($senderName)
    {
        return $this->setData(self::SENDER_NAME, $senderName);
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderEmail()
    {
        return $this->getData(self::SENDER_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderEmail($senderEmail)
    {
        return $this->setData(self::SENDER_EMAIL, $senderEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientName()
    {
        return $this->getData(self::RECIPIENT_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientName($recipientName)
    {
        return $this->setData(self::RECIPIENT_NAME, $recipientName);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientEmail()
    {
        return $this->getData(self::RECIPIENT_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientEmail($recipientEmail)
    {
        return $this->setData(self::RECIPIENT_EMAIL, $recipientEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryDate()
    {
        return $this->getData(self::DELIVERY_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setDeliveryDate($deliveryDate)
    {
        return $this->setData(self::DELIVERY_DATE, $deliveryDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryDateTimezone()
    {
        return $this->getData(self::DELIVERY_DATE_TIMEZONE);
    }

    /**
     * {@inheritdoc}
     */
    public function setDeliveryDateTimezone($deliveryDateTimezone)
    {
        return $this->setData(self::DELIVERY_DATE_TIMEZONE, $deliveryDateTimezone);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailSent()
    {
        return $this->getData(self::EMAIL_SENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailSent($emailSent)
    {
        return $this->setData(self::EMAIL_SENT, $emailSent);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeadline()
    {
        return $this->getData(self::HEADLINE);
    }

    /**
     * {@inheritdoc}
     */
    public function setHeadline($headline)
    {
        return $this->setData(self::HEADLINE, $headline);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentHistoryAction()
    {
        return $this->getData(self::CURRENT_HISTORY_ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentHistoryAction($value)
    {
        return $this->setData(self::CURRENT_HISTORY_ACTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\Giftcard\Api\Data\GiftcardExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        if (null === $this->getId()) {
            if (null === $this->getType()) {
                $this->setType(GiftcardType::VALUE_VIRTUAL);
            }
            if ($this->isExpired()) {
                throw new LocalizedException(__('Expiration date cannot be in the past'));
            }
            $this->setBalance($this->getInitialBalance());
            if (null === $this->getCode()) {
                $codes = $this->giftcardManagement->generateCodes($this->getWebsiteId());
                $this->setCode(array_shift($codes));
            }
        }
        $this->attachGiftcardState();

        if ($this->getBalance() < 0) {
            $this->setBalance(0);
        }
        if (null === $this->getEmailSent()) {
            $this->setEmailSent(EmailStatus::AWAITING);
        }

        // Check if change state (Activate / Deactivate)
        if ($this->getState() == Status::ACTIVE && $this->getBalance() <= 0) {
            throw new LocalizedException(__('Unable to activate Gift Card code'));
        }
        if (($this->getOrigData('state') == Status::USED || $this->getOrigData('state') == Status::EXPIRED)
            && $this->getState() == Status::DEACTIVATED
        ) {
            throw new LocalizedException(__('Unable to deactivate Gift Card code'));
        }
        if ($this->getState() == Status::DEACTIVATED
            && $this->getOrigData('balance') === 0
            && $this->getOrigData('balance') != $this->getBalance()
        ) {
            throw new LocalizedException(__('Unable to change balance of deactivated Gift Card code'));
        }

        if ((($this->getOrigData('state') != Status::DEACTIVATED && $this->getState() == Status::DEACTIVATED)
                || ($this->getOrigData('state') != Status::EXPIRED && $this->getState() == Status::EXPIRED)
                || ($this->getOrigData('state') != Status::USED && $this->getState() == Status::USED)
                || (empty($this->getEmailTemplate()) || $this->getEmailTemplate() == EmailTemplate::DO_NOT_SEND))
            && $this->getEmailSent() != EmailStatus::SENT
        ) {
            $this->setEmailSent(EmailStatus::NOT_SEND);
        }
        if ((($this->getOrigData('state')
                    && $this->getOrigData('state') != Status::ACTIVE && $this->getState() == Status::ACTIVE)
                || ($this->getOrigData('email_template') == EmailTemplate::DO_NOT_SEND
                    && $this->getEmailTemplate() != EmailTemplate::DO_NOT_SEND))
            && $this->getEmailSent() != EmailStatus::SENT
        ) {
            $this->setEmailSent(EmailStatus::AWAITING);
        }
        if ($this->getType() == GiftcardType::VALUE_PHYSICAL) {
            $this
                ->setSenderEmail('')
                ->setRecipientEmail('')
                ->setEmailTemplate(EmailTemplate::DO_NOT_SEND)
                ->setEmailSent(EmailStatus::NOT_SEND);
        }
        if (empty($this->getDeliveryDate())) {
            $this->setDeliveryDate(null);
        }
        if (empty($this->getExpireAt())) {
            $this->setExpireAt(null);
        } else {
            if ($this->getExpireAt() instanceof \DateTime) {
                $expireAt = $this->getExpireAt();
            } else {
                $expireAt = new \DateTime($this->getExpireAt(), new \DateTimeZone('UTC'));
            }
            $this->setExpireAt(
                $expireAt->setTime(0, 0, 0)->format(StdlibDateTime::DATETIME_PHP_FORMAT)
            );
        }

        if ($this->getId() && $this->getOrderId() && ($this->getOrigData('headline') != $this->getHeadline()
                || $this->getOrigData('message') != $this->getMessage())
        ) {
            $comment = __('Headline and/or Message has been changed for Gift Card code %1', $this->getCode());
            $this->giftcardManagement->addCommentToGiftcardOrder($this, $comment);
        }

        $this->updateUsedStatData();
        return $this;
    }

    /**
     * Attach Gift Card state
     *
     * @return $this
     */
    private function attachGiftcardState()
    {
        $state = $this->getState();

        if (null == $state) {
            $state = Status::ACTIVE;
        }
        if ($state != Status::DEACTIVATED) {
            if (!($this->getBalance() > 0)) {
                $state = Status::USED;
            } elseif ($this->isExpired()) {
                $state = Status::EXPIRED;
            } else {
                $state = Status::ACTIVE;
            }
        }

        $this->setState($state);
        return $this;
    }

    /**
     * Check is expired or not
     *
     * @return bool
     */
    private function isExpired()
    {
        $website = $this->storeManager->getWebsite($this->getWebsiteId());
        $expired = new \DateTime($this->getExpireAt());
        $expired->setTime(23, 59, 59);

        return $this->getWebsiteDate($website) > $expired;
    }

    /**
     * Retrieve website date
     *
     * @param WebsiteInterface $website
     * @return string
     */
    private function getWebsiteDate($website)
    {
        $websiteTimezone = $this->localeDate->getConfigTimezone(ScopeInterface::SCOPE_WEBSITE, $website->getCode());
        $now = new \DateTime(null, new \DateTimeZone($websiteTimezone));
        $now->setTimezone(new \DateTimeZone('UTC'));

        return $now;
    }

    /**
     * Update used statistics
     *
     * @return void
     */
    private function updateUsedStatData()
    {
        if ($this->getProductId()) {
            $data = false;
            if ($this->getOrigData('state') != Status::USED && $this->getState() == Status::USED) {
                $data = ['used_qty' => 1];
            }
            if ($this->getOrigData('state') == Status::USED
                && ($this->getState() == Status::ACTIVE || $this->getState() == Status::DEACTIVATED)
            ) {
                $data = ['used_qty' => -1];
            }

            if ($data) {
                // Save used_qty for default store view
                $this->statistics->updateStatistics(
                    $this->getProductId(),
                    Store::DEFAULT_STORE_ID,
                    $data
                );
            }
        }
    }
}
