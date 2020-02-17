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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model;

use Magento\Framework\DataObject\IdentityInterface;

class Redirect extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'seo_redirect';

    /**
     * @var string
     */
    protected $_cacheTag = 'seo_redirect';//@codingStandardsIgnoreLine
    /**
     * @var string
     */
    protected $_eventPrefix = 'seo_redirect';//@codingStandardsIgnoreLine

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
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Seo\Model\ResourceModel\Redirect');
    }
}
