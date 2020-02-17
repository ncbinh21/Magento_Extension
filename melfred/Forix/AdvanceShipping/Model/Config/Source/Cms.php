<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredboarzall
 * Date: 10/07/2018
 * Time: 11:04
 */
namespace Forix\AdvanceShipping\Model\Config\Source;

/**
 * Class Cms
 * @package IWD\StoreLocator\Model\Config\Source
 */
class Cms implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Cms\Model\Block
     */
    private $block;

    /**
     * Cms constructor.
     * @param \Magento\Cms\Model\Block $block
     */
    public function __construct(\Magento\Cms\Model\BlockFactory $block)
    {
        $this->block = $block;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->block->create()->getCollection()->addFieldToFilter('is_active', ['eq' => 1])->load();
        $items = [];
        $item = ['value' => '', 'label' =>__('-- Please Select CMS Block --')];
        $items[] = $item;

        foreach ($collection as $block) {
            $item = ['value' => $block->getIdentifier(), 'label' =>$block->getTitle()];
            $items[] = $item;
        }
        
        return $items;
    }
}
