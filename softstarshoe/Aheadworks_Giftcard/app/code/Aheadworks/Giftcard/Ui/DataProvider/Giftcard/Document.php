<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\DataProvider\Giftcard;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document as FrameworkDocument;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Document
 *
 * @package Aheadworks\Giftcard\Ui\DataProvider\Giftcard
 */
class Document extends FrameworkDocument
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param AttributeValueFactory $attributeValueFactory
     * @param TimezoneInterface $localeDate
     * @param ResolverInterface $localeResolver
     */
    public function __construct(
        AttributeValueFactory $attributeValueFactory,
        TimezoneInterface $localeDate,
        ResolverInterface $localeResolver
    ) {
        parent::__construct($attributeValueFactory);
        $this->localeDate = $localeDate;
        $this->locale = $localeResolver->getLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttribute($attributeCode)
    {
        if ($attributeCode == 'created_at') {
            $createdAt = $this->getData($attributeCode);

            if ($createdAt) {
                $convertedDate = $this->localeDate->date(
                    new \DateTime($createdAt, new \DateTimeZone('UTC')),
                    $this->locale,
                    true
                );
                $this->setCustomAttribute($attributeCode, $convertedDate->format('M j, y h:i:s A'));
            }
        }

        if ($attributeCode == 'expire_at') {
            $expireAt = $this->getData($attributeCode);

            if ($expireAt) {
                $convertedDate = $this->localeDate->date(
                    new \DateTime($expireAt, new \DateTimeZone('UTC')),
                    $this->locale,
                    true
                );
                $this->setCustomAttribute($attributeCode, $convertedDate->setTime(0, 0, 0)->format('M j, y'));
            }
        }

        if ($attributeCode == 'delivery_date') {
            $deliveryDate = $this->getData($attributeCode);

            if ($deliveryDate) {
                $convertedDate = $this->localeDate->date(
                    new \DateTime($deliveryDate, new \DateTimeZone('UTC')),
                    $this->locale,
                    true
                );
                $this->setCustomAttribute($attributeCode, $convertedDate->format('M j, y h:i A'));
            }
        }
        return parent::getCustomAttribute($attributeCode);
    }
}
