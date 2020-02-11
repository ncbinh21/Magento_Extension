<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Product\Entity\Attribute\Backend;

use Magento\Framework\Exception\LocalizedException;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;

/**
 * Class Amounts
 *
 * @package Aheadworks\Giftcard\Model\Product\Entity\Attribute\Backend
 */
class Amounts extends AbstractBackend
{
    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        $this->validateAmounts($object);
        $this->validateOpenAmount($object);

        return $this;
    }

    /**
     * Validate amounts
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     * @throws LocalizedException
     */
    private function validateAmounts($object)
    {
        $amountsRows = $object->getData($this->getAttribute()->getName()) ? : [];
        $amountsKeys = [];
        foreach ($amountsRows as $data) {
            if (!isset($data['price']) || !empty($data['delete'])) {
                continue;
            }
            if ($data['website_id'] == 0) {
                foreach ($object->getWebsiteIds() as $websiteId) {
                    $key = implode('-', [$websiteId, (float)$data['price']]);
                    $amountsKeys[$key] = $this->processValidateDuplicate($key, $amountsKeys);
                }
            }
            $key = implode('-', [$data['website_id'], (float)$data['price']]);
            $amountsKeys[$key] = $this->processValidateDuplicate($key, $amountsKeys);
        }
        if (count($amountsKeys) == 0 && !$object->getData(ProductAttributeInterface::CODE_AW_GC_ALLOW_OPEN_AMOUNT)) {
            throw new LocalizedException(__('Specify amount options'));
        }

        return $this;
    }

    /**
     * Validate on duplicate amount
     *
     * @param string $key
     * @param [] $amountsKeys
     * @return bool
     * @throws LocalizedException
     */
    private function processValidateDuplicate($key, $amountsKeys)
    {
        if (array_key_exists($key, $amountsKeys)) {
            throw new LocalizedException(__('Duplicate amount found'));
        }
        return true;
    }

    /**
     * Validate open amount
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     * @throws LocalizedException
     */
    private function validateOpenAmount($object)
    {
        if ((int)$object->getData(ProductAttributeInterface::CODE_AW_GC_ALLOW_OPEN_AMOUNT)) {
            $minAmount = (int)$object->getData(ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MIN);
            $maxAmount = (int)$object->getData(ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MAX);

            if ($minAmount > $maxAmount) {
                throw new LocalizedException(
                    __('"Open Amount Max Value" must be greater than "Open Amount Min Value".')
                );
            }
        }

        return $this;
    }
}
