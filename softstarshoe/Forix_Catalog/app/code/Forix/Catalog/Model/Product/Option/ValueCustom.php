<?php

namespace Forix\Catalog\Model\Product\Option;

class ValueCustom extends \Magento\Catalog\Model\Product\Option\Value
{
    /**
     * @return $this
     */
    public function saveValues()
    {
        foreach ($this->getValues() as $value) {
            $this->isDeleted(false);
            $this->setData(
                $value
            )->setData(
                'option_id',
                $this->getOption()->getId()
            )->setData(
                'store_id',
                $this->getOption()->getStoreId()
            );

            if ($this->getData('is_delete') == '1') {
                if ($this->getId()) {
                    $this->deleteValues($this->getId());
                    $this->delete();
                }
            } else {
                $this->save();
            }
        }
        //eof foreach()
        return $this;
    }
}