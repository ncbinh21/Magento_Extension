<?php
namespace Forix\AlternateImage\Plugin\Model;

use Magento\Catalog\Model\Layer;

/**
 * Class ListProductPlugin
 *
 * @package Forix\AlternateImage\Plugin\Block
 */
class LayerPlugin
{

    /**
     * @param Layer $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $result
     * @return mixed
     */
    public function afterGetProductCollection(
        Layer $subject,
        $result
    ) {
        return $result->addAttributeToSelect('alternate_image');
    }
}