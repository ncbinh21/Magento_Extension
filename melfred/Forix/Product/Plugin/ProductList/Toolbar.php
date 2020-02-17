<?php

namespace Forix\Product\Plugin\ProductList;

use function GuzzleHttp\Psr7\str;

class Toolbar
{
    protected $request;

    public function __construct(
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->request = $request;
    }


    public function afterSetCollection(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, $result)
    {
        if ($this->request->getParam('mb_ground_condition') || isset($this->request->getParam('amshopby')['mb_ground_condition'])) {
            if (!strpos($result->getCollection()->getSelect()->__toString(), 'ground_condition_weight')) {
                $queryParam = $this->request->getParam('mb_ground_condition') ? $this->request->getParam('mb_ground_condition') : $this->request->getParam('amshopby')['mb_ground_condition'][0];
                $queryParam = explode(',', $queryParam);
                $strCondition = '(';
                foreach ($queryParam as $param) {
                    $strCondition .= ' groundCondition.w_'.$param. ' +';
                }
                //replace last char + => )
                $strCondition =  substr_replace($strCondition, ")", -1);
                $result->getCollection()->getSelect()->joinLeft(
                    ['groundCondition' => 'ground_condition_weight'],
                    "groundCondition.pid = e.entity_id",
                    ['weight' => $strCondition]
                )->order('weight desc');
                
            }
        }


        return $result;
    }


}



