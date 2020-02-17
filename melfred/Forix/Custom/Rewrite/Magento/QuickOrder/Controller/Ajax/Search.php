<?php

namespace Forix\Custom\Rewrite\Magento\QuickOrder\Controller\Ajax;

class Search extends \Magento\QuickOrder\Controller\Ajax\Search
{
    /**
     * @param array $items
     * @return array
     */
    protected function removeEmptySkuItems(array $items)
    {
        foreach ($items as $k => $item) {
            if (empty($item['sku'])) {
                unset($items[$k]);
            } else {
                $sku = $item['sku'];
                if(isset($item['qty'])) {
                    $qty = $item['qty'];
                    $items[$k]['qty'] = $qty;
                }
                $items[$k]['sku'] = trim($sku);
            }
        }

        return $items;
    }
}