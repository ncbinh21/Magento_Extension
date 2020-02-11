<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\SeoObject\Wrapper;

class Filter extends \Magento\Framework\DataObject
{
    /**
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Resolver $layerResolver
    ) {
        $this->layerResolver = $layerResolver;
        parent::__construct();
        $options = [];
        $names = [];
        $code = false;
        foreach ($this->getActiveFilters() as $filter) {
            if (!$filter->getData('filter')) {
                continue; // to prevent "The filter must be an object. Please set a correct filter" error.
            }
            if (!$filter->getFilter()->getData('attribute_model')) {
                continue;
            }
            if (is_object($filter->getFilter()->getAttributeModel())) {
                $code = $filter->getFilter()->getAttributeModel()->getAttributeCode();
            }
            $name = $filter->getName();
            $selected = $filter->getLabel();
            if (!isset($options[$code])) {
                $options[$code] = [];
            }
            $names[$code] = $name;
            $options[$code][] = $selected;
        }
        $allOptions = [];
        $allOptionsB = [];
        foreach ($options as $code => $values) {
            if (isset($values[0]) && is_array($values[0])) {
                $values = $values[0];
            }
            $this->setData($code, implode(', ', $values));
            //
            //            if ($code == 'brand') {
            //                continue;
            //            }
            $allOptions[] = $names[$code].': '.implode(', ', $values);
            $allOptionsB[] = implode(', ', $values);
        }

        $this->setNamedSelectedOptions(implode(', ', $allOptions));
        $this->setSelectedOptions(implode(', ', $allOptionsB));
    }

    /**
     * Retrieve Layer object.
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        if (!$this->hasData('layer')) {
            $this->setLayer($this->layerResolver->get());
        }

        return $this->_getData('layer');
    }

    /**
     * Retrieve active filters.
     *
     * @return array
     */
    public function getActiveFilters()
    {
        if (!$this->getLayer()) { //in tests
            return [];
        }
        $filters = $this->getLayer()->getState()->getFilters();
        if (!is_array($filters)) {
            $filters = [];
        }

        return $filters;
    }
}
