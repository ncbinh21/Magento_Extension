<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Product\Option;

use Aheadworks\Giftcard\Api\Data\OptionInterface;
use Magento\Framework\Escaper;
use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardType;
use Magento\Config\Model\Config\Source\Locale\Timezone as TimezoneSource;

/**
 * Class Render
 *
 * @package Aheadworks\Giftcard\Model\Product\Option
 */
class Render
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const GLOBAL_SECTION = 'global';
    const BACKEND_SECTION = 'backend';
    const FRONTEND_SECTION = 'frontend';
    /**#@-*/

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var GiftcardType
     */
    private $giftcardType;

    /**
     * @var TimezoneSource
     */
    private $timezoneSource;

    /**
     * @var []
     */
    private $optionsConfig;

    /**
     * @param Escaper $escaper
     * @param GiftcardType $giftcardType
     * @param TimezoneSource $timezoneSource
     * @param [] $optionConfig
     */
    public function __construct(
        Escaper $escaper,
        GiftcardType $giftcardType,
        TimezoneSource $timezoneSource,
        $optionsConfig = []
    ) {
        $this->escaper = $escaper;
        $this->giftcardType = $giftcardType;
        $this->optionsConfig = $optionsConfig;
        $this->timezoneSource = $timezoneSource;
    }

    /**
     * Retrieve data for display
     *
     * @param [] $data
     * @param string $section
     * @return []
     */
    public function render($data, $section = self::GLOBAL_SECTION)
    {
        $result = [];
        $data = $this->prepareDataBySection($data, $section);

        if (isset($data[OptionInterface::GIFTCARD_TYPE])) {
            $type = $this->giftcardType->getOptionText($data[OptionInterface::GIFTCARD_TYPE]);
            $result[] = [
                'label' => __('Gift Card Type'),
                'value' => $this->escaper->escapeHtml($type)
            ];
        }

        if (isset($data[OptionInterface::TEMPLATE_NAME])) {
            $result[] = [
                'label' => __('Gift Card Design'),
                'value' => $this->escaper->escapeHtml($data[OptionInterface::TEMPLATE_NAME])
            ];
        }

        if (isset($data[OptionInterface::DELIVERY_DATE])) {
            $result[] = [
                'label' => __('Gift Card Delivery Date'),
                'value' => $this->escaper->escapeHtml($data[OptionInterface::DELIVERY_DATE])
            ];
        }

        if (isset($data[OptionInterface::DELIVERY_DATE_TIMEZONE])) {
            $result[] = [
                'label' => __('Gift Card Delivery Date Timezone'),
                'value' => $this->escaper->escapeHtml(
                    $this->getTimezoneLabelByValue($data[OptionInterface::DELIVERY_DATE_TIMEZONE])
                )
            ];
        }

        if (isset($data[OptionInterface::SENDER_NAME])) {
            $senderName = $data[OptionInterface::SENDER_NAME];
            if (isset($data[OptionInterface::SENDER_EMAIL])) {
                $senderEmail = $data[OptionInterface::SENDER_EMAIL];
            }
            $result[] = [
                'label' => __('Gift Card Sender'),
                'value' => $this->escaper->escapeHtml(
                    isset($senderEmail) ? $senderName . ' <' . $senderEmail . '>' : $senderName
                )
            ];
        }

        if (isset($data[OptionInterface::RECIPIENT_NAME])) {
            $recipientName = $data[OptionInterface::RECIPIENT_NAME];
            if (isset($data[OptionInterface::RECIPIENT_EMAIL])) {
                $recipientEmail = $data[OptionInterface::RECIPIENT_EMAIL];
            }
            $result[] = [
                'label' => __('Gift Card Recipient'),
                'value' => $this->escaper->escapeHtml(
                    isset($recipientEmail) ? $recipientName . ' <' . $recipientEmail . '>' : $recipientName
                )
            ];
        }

        if (isset($data[OptionInterface::HEADLINE])) {
            $headline = trim($data[OptionInterface::HEADLINE]);
            if ($headline) {
                $result[] = [
                    'label' => __('Gift Card Headline'),
                    'value' => $this->escaper->escapeHtml($headline)
                ];
            }
        }

        if (isset($data[OptionInterface::MESSAGE])) {
            $message = trim($data[OptionInterface::MESSAGE]);
            if ($message) {
                $result[] = [
                    'label' => __('Gift Card Message'),
                    'value' => $this->escaper->escapeHtml($message)
                ];
            }
        }

        if (isset($data[OptionInterface::GIFTCARD_CODES])) {
            $codes = $data['aw_gc_created_codes'];
            if (is_array($codes) && count($codes) > 0) {
                $result[] = [
                    'label' => __('Gift Card Codes'),
                    'value' => implode('<br/>', $this->escaper->escapeHtml($codes)),
                    'custom_view' => true
                ];
            }
        }
        return $result;
    }

    /**
     * Prepare data by section
     *
     * @param [] $data
     * @param string $section
     * @return []
     */
    private function prepareDataBySection($data, $section)
    {
        if ($section == self::GLOBAL_SECTION) {
            return $data;
        }

        foreach ($this->optionsConfig as $optionConfig) {
            $remove = true;
            if (!isset($data[$optionConfig['optionName']]) || !isset($data[$optionConfig['optionName']])) {
                continue;
            }
            if (!isset($optionConfig['sections']) || !is_array($optionConfig['sections'])) {
                continue;
            }

            foreach ($optionConfig['sections'] as $optionSection) {
                if ($section == $optionSection) {
                    $remove = false;
                }
            }
            if ($remove) {
                unset($data[$optionConfig['optionName']]);
            }
        }
        return $data;
    }

    /**
     * Retrieve timezone label by value
     *
     * @param string $value
     * @return string
     */
    private function getTimezoneLabelByValue($value)
    {
        $options = $this->timezoneSource->toOptionArray();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return '';
    }
}
