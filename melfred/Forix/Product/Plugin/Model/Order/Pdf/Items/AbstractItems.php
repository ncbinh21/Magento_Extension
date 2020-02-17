<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */
namespace Forix\Product\Plugin\Model\Order\Pdf\Items;

class AbstractItems
{
    public function afterGetItemOptions(\Magento\Sales\Model\Order\Pdf\Items\AbstractItems $subject, $result)
    {
        $rigModel = [];
        if (isset($result) && !empty($result)){
            foreach ($result as $key => $value) {
                if ($value['label'] == __("Your Rig Model")){
                    $rigModel = $result[$key];
                    unset($result[$key]);
                    break;
                }
            }
        }
        if($rigModel) {
            array_unshift($result, $rigModel);
        }
        return $result;
    }
}