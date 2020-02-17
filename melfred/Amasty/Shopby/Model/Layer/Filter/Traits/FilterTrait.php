<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Model\Layer\Filter\Traits;

trait FilterTrait
{
    /**
     * @var current applied value
     */
    protected $currentValue;

    /**
     * @param set $currentValue
     */
    protected function setCurrentValue($currentValue)
    {
        $this->currentValue = $currentValue;
    }

    /**
     * @return bool is filter applied
     */
    protected function hasCurrentValue()
    {
        return $this->currentValue != null;
    }

    public function isApplied()
    {
        foreach ($this->getLayer()->getState()->getFilters() as $filter) {
            if ($filter->getFilter()->getRequestVar() == $this->getRequestVar()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Must not remove filter with one option if it is applied.
     *
     * @param array $itemsData
     * @return array
     */
    protected function getReducedItemsData(array $itemsData)
    {
        $isFilterActive = false;
        foreach ($this->getLayer()->getState()->getFilters() as $filter) {
            if ($filter->getFilter()->getRequestVar() == $this->getRequestVar()) {
                $isFilterActive = true;
                break;
            }
        }

        return $isFilterActive ? $itemsData : [];
    }
}
