<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 27
 * Time: 14:51
 */

namespace Forix\InvoicePrint\Observer\Sales;


abstract class AbstractConvert implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @param $source \Magento\Framework\Model\AbstractModel
     * @param $destination \Magento\Framework\Model\AbstractModel
     * @param array|string $attributeCodes
     * @return $this
     */
    public function convert($source, $destination, $attributeCodes)
    {
        if(!is_array($attributeCodes)){
            $attributeCodes = [$attributeCodes];
        }
        foreach ($attributeCodes as $attributeCode) {
            if($source->getData($attributeCode)) {
                $destination->setData($attributeCode, $source->getData($attributeCode));
            }
        }
        return $this;
    }
}