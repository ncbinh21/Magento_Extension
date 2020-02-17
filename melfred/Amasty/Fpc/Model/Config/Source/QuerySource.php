<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Config\Source;

class QuerySource implements \Magento\Framework\Option\ArrayInterface
{
    const SOURCE_ALL_PAGES  = 0;
    const SOURCE_SITE_MAP   = 1;
    const SOURCE_TEXT_FILE  = 2;
    const SOURCE_COMBINE  = 3;

    public function toOptionArray()
    {
        $options = [];

        $options[] = [
            'label' => __('Pages Types'),
            'value' => self::SOURCE_ALL_PAGES
        ];

        $options[] = [
            'label' => __('Text file with one link per line'),
            'value' => self::SOURCE_TEXT_FILE
        ];

        $options[] = [
            'label' => __('Sitemap XML'),
            'value' => self::SOURCE_SITE_MAP
        ];

        $options[] = [
            'label' => __('Sitemap XML and Text File together'),
            'value' => self::SOURCE_COMBINE
        ];

        return $options;
    }
}
