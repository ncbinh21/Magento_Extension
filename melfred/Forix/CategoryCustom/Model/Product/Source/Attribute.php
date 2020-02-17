<?php

namespace Forix\CategoryCustom\Model\Product\Source;

class Attribute implements \Magento\Framework\Data\OptionSourceInterface
{

    protected $collectionFactory;

    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }


    public function getAttributeVisible()
    {
        $attribute = $this->collectionFactory->create();
        $attribute->addFieldToSelect(['attribute_code','frontend_label'])->joinLeft(
            ['ceav' => 'catalog_eav_attribute'],
            'ceav.attribute_id = main_table.attribute_id',null
        )->addFieldToFilter('ceav.is_visible_on_front','1')->setOrder('frontend_label','asc');
        return $attribute;
    }

    public function toOptionArray()
    {
        $optionGroups = [];
        $optionGroups[] = [
            'label' => '',
            'value' => (string)'',
        ];

        $groups = $this->getAttributeVisible()->getData();
        foreach ($groups as $group) {
            $optionGroups[] = [
                'label' => $group["frontend_label"] .' ('.$group['attribute_code'].')' ,
                'value' => $group["attribute_code"],
            ];
        }

        return $optionGroups;
    }
}
