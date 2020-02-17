<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/07/2018
 * Time: 11:42
 */
namespace Forix\Product\Plugin\Model;
class AttributeOptionProvider
{
    /**
     * @param \Magento\ConfigurableProduct\Model\AttributeOptionProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetAttributeOptions($subject, $result){
        foreach ($result as &$item){
            if(isset($item['recommend_sku']) && $item['recommend_sku']){
                $item['default_title'] = $item['default_title'] . " " . __('Recommend'); 
                $item['option_title'] = $item['option_title'] . " " . __('Recommend'); 
            }
        }
        return $result;
    }
}