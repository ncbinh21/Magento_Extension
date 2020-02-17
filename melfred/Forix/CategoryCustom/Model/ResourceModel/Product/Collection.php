<?php
/**
 * Created by PhpStorm.
 * User: kuton
 * Date: 18/6/18
 * Time: 4:27 PM
 */
namespace Forix\CategoryCustom\Model\ResourceModel\Product;
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    const CACHE_TAG = 'compare_soil_type';

    /**
     * @return $this
     */
    public function addGroundConditionToSelect(){
        $this->_select
            ->joinLeft(
            ['groundCondition' => 'ground_condition_weight'],
            "groundCondition.pid = e.entity_id", 'groundCondition.*');
        return $this;
    }

}
