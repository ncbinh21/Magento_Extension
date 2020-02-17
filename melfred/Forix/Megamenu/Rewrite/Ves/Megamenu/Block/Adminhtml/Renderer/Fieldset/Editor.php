<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: html
 */
namespace Forix\Megamenu\Rewrite\Ves\Megamenu\Block\Adminhtml\Renderer\Fieldset;

class Editor extends \Ves\Megamenu\Block\Adminhtml\Renderer\Fieldset\Editor {
    protected $_template = 'Forix_Megamenu::editor.phtml';
    public function getAllAttributeOptions(){
        $collection = $this->_objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection');
        $collection->addIsFilterableFilter()
            ->join(
                array('options'=>$collection->getConnection()->getTableName('eav_attribute_option')),
                'options.attribute_id = main_table.attribute_id',
                array('option_id')
            )
            ->join(
                array('option_values'=>$collection->getConnection()->getTableName('eav_attribute_option_value')),
                'options.option_id = option_values.option_id AND option_values.store_id = 0',
                array('value')
            )
            ->setOrder('position', 'ASC')
            ->setOrder('sort_order', 'ASC');

        $select = $collection->getSelect();
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns(array('attribute_code','frontend_label'), 'main_table')
            ->columns('option_id', 'options')
            ->columns('value', 'option_values');
        $result = array('attributes'=>array(array('name'=>'Please Choose...','value'=>null)),'options'=>array());
        $rawData = $collection->getConnection()->fetchAll($select);
        $existedCode = array();
        foreach ($rawData as $k=>$v){
            if(!array_key_exists($v['attribute_code'],$existedCode)){
                array_push($result['attributes'],array('name'=>html_entity_decode($v['frontend_label']),'value'=>$v['attribute_code']));
                $result['options'][$v['attribute_code']][] = array('name'=>'Please Choose...','value'=>null);
                $existedCode [$v['attribute_code']] = true;
            }
            $result['options'][$v['attribute_code']][] = array('name'=>html_entity_decode($v['value']),'value'=>$v['option_id']);

        }
        return json_encode($result);
    }
}