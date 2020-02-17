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



namespace Mirasvit\SeoSitemap\Model\Config\Source\Ignore\Cms;

class Pages implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    protected $pageCollectionFactory;

    /**
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory
     */
    public function __construct(
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory
    ) {
        $this->pageCollectionFactory = $pageCollectionFactory;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $collection = $this->pageCollectionFactory->create()
                             ->addFieldToFilter('is_active', true)
                             ;
        $result = [];
        foreach ($collection as $v) {
            $result[$v['identifier']] = $v['title'];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->toArray() as $k => $v) {
            $result[] = ['value' => $k, 'label' => $v];
        }

        return $result;
    }

    /************************/
}
