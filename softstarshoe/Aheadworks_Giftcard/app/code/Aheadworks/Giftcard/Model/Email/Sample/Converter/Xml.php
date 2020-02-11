<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Email\Sample\Converter;

use Magento\Framework\Config\ConverterInterface;

/**
 * Class Xml
 *
 * @package Aheadworks\Giftcard\Model\Email\Sample\Converter
 */
class Xml implements ConverterInterface
{
    /**
     * Converting data to array type
     *
     * @param mixed $source
     * @return array
     * @throws \InvalidArgumentException
     */
    public function convert($source)
    {
        $output = [];
        if (!$source instanceof \DOMDocument) {
            return $output;
        }

        $events = $source->getElementsByTagName('template');
        foreach ($events as $event) {
            $eventData = [];
            /** @var $event \DOMElement */
            foreach ($event->childNodes as $child) {
                if (!$child instanceof \DOMElement) {
                    continue;
                }
                /** @var $event \DOMElement */
                $eventData[$child->nodeName] = $child->nodeValue;
            }
            $output[] = $eventData;
        }
        return $output;
    }
}
