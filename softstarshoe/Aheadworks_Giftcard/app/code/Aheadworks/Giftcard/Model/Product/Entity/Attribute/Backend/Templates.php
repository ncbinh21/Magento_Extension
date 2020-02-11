<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Product\Entity\Attribute\Backend;

use Magento\Framework\Exception\LocalizedException;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardType;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;

/**
 * Class Templates
 *
 * @package Aheadworks\Giftcard\Model\Product\Entity\Attribute\Backend
 */
class Templates extends AbstractBackend
{
    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        if ($object->getData(ProductAttributeInterface::CODE_AW_GC_TYPE) == GiftcardType::VALUE_PHYSICAL) {
            return $this;
        }

        $templatesRows = $object->getData($this->getAttribute()->getName()) ? : [];
        $templatesKeys = [];
        foreach ($templatesRows as $data) {
            if (!isset($data['template']) || !empty($data['delete'])) {
                continue;
            }
            if ($data['store_id'] == 0) {
                foreach ($object->getStoreIds() as $storeId) {
                    $key = implode('-', [$storeId, (float)$data['template']]);
                    $templatesKeys[$key] = $this->processValidateDuplicate($key, $templatesKeys);
                }
            }
            $key = implode('-', [$data['store_id'], (float)$data['template']]);
            $templatesKeys[$key] = $this->processValidateDuplicate($key, $templatesKeys);
        }
        if (count($templatesKeys) == 0) {
            throw new LocalizedException(__('Specify template options'));
        }
        return $this;
    }

    /**
     * Validate on duplicate template
     *
     * @param string $key
     * @param [] $templatesKeys
     * @return bool
     * @throws LocalizedException
     */
    private function processValidateDuplicate($key, $templatesKeys)
    {
        if (array_key_exists($key, $templatesKeys)) {
            throw new LocalizedException(__('Duplicate template found'));
        }
        return true;
    }
}
