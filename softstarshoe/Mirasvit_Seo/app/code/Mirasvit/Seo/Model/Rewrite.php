<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model;

use Magento\Framework\DataObject\IdentityInterface;

class Rewrite extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'seo_rewrite';

    /**
     * @var string
     */
    protected $_cacheTag = 'seo_rewrite';//@codingStandardsIgnoreLine

    /**
     * @var string
     */
    protected $_eventPrefix = 'seo_rewrite';//@codingStandardsIgnoreLine

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Seo\Model\ResourceModel\Rewrite');
    }
}
