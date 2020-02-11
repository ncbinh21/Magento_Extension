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



namespace Mirasvit\SeoAutolink\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Link
 * @package Mirasvit\SeoAutolink\Model
 *
 * @method int getLinkId()
 * @method $this setLinkId(int $param)
 * @method string getKeyword()
 * @method $this setKeyword(string $param)
 * @method string getUrl()
 * @method $this setUrl(string $param)
 * @method string getUrlTarget()
 * @method $this setUrlTarget(string $param)
 * @method string getUrlTitle()
 * @method $this setUrlTitle(string $param)
 * @method int getIsNofollow()
 * @method $this setIsNofollow(int $param)
 * @method int getMaxReplacements()
 * @method $this setMaxReplacements(int $param)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $param)
 * @method int getOccurence()
 * @method $this setOccurence(int $param)
 * @method int getIsActive()
 * @method $this setIsActive(int $param)
 * @method string getActiveFrom()
 * @method $this setActiveFrom(string $param)
 * @method string getActiveTo()
 * @method $this setActiveTo(string $param)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $param)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $param)
 * @method $this setStoreIds(array $param)
 *
 */
class Link extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'seoautolink_link';
    /**
     * @var string
     */
    protected $_cacheTag = 'seoautolink_link';//@codingStandardsIgnoreLine
    /**
     * @var string
     */
    protected $_eventPrefix = 'seoautolink_link';//@codingStandardsIgnoreLine

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
        $this->_init('Mirasvit\SeoAutolink\Model\ResourceModel\Link');
    }

    /**
     * @param string $keyword
     * @return bool|\Magento\Framework\DataObject
     */
    public function loadByKeyword($keyword)
    {
        $collection = $this->getCollection()
                        ->addFieldToFilter('keyword', $keyword);
        if ($collection->count() > 0) {
            return $collection->getFirstItem();
        }

        return false;
    }
}
