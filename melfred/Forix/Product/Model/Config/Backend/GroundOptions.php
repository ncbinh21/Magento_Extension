<?php

namespace Forix\Product\Model\Config\Backend;

class GroundOptions extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    protected $_helper;
    protected $connection;
    protected $attOptions;
    protected $groundIndex;


    public function __construct(
        \Forix\Product\Model\Source\GroundCondition $attOptions,
        \Forix\Product\Model\GroundIndex $groundIndex,
        \Forix\Product\Helper\Data $data
    )
    {
        $this->_helper = $data;
        $this->attOptions = $attOptions;
        $this->groundIndex = $groundIndex;
    }

    public function beforeSave($object)
    {
        $data = $this->groundIndex->processData($object->getData('entity_id'),$object->getData('mb_soil_type_best'),$object->getData('mb_soil_type_better'),$object->getData('mb_soil_type_good'));
        $indexData = $data['indexdata'];
        //        $data = $this->calculateCondition($object);
        $attributeCode = $this->getAttribute()->getAttributeCode();
        if (is_array($data['condition'])) {
            $data = array_filter($data['condition'], function ($value) {
                return $value === '0' || !empty($value);
            });
            $object->setData($attributeCode, implode(',', $data));
        }

        $this->groundIndex->updateRow($indexData);

        return parent::beforeSave($object);
    }

}
