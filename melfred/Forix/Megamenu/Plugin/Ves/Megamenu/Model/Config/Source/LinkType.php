<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: html
 */
namespace Forix\Megamenu\Plugin\Ves\Megamenu\Model\Config\Source;
class LinkType {
    public function afterToOptionArray(\Ves\Megamenu\Model\Config\Source\LinkType $subject, $result)
    {
        array_push($result,[
            'label' => __('Attribute Link'),
            'value' => 'attribute_link'
        ]);
        return $result;
    }
}