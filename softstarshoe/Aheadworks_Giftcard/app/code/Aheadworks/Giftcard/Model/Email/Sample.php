<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Email;

use Magento\Framework\Config\Data as ConfigData;
use Aheadworks\Giftcard\Model\Email\Sample\Reader\Xml as XmlReader;
use Magento\Framework\Config\CacheInterface;

/**
 * Class Sample
 *
 * @package Aheadworks\Giftcard\Model\Email
 */
class Sample extends ConfigData
{
    /**
     * @param XmlReader $reader
     * @param CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        XmlReader $reader,
        CacheInterface $cache,
        $cacheId = 'aheadworks_giftcard_sample_email_templates_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }
}
