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
 * @package   mirasvit/module-report
 * @version   1.2.27
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Report\Model\Export;

use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Filters;
use Magento\Ui\Component\Filters\Type\Select;
use Magento\Ui\Component\Listing\Columns;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class MetadataProvider extends \Magento\Ui\Model\Export\MetadataProvider
{
    /**
     * @var \Mirasvit\Report\Model\Config\Map
     */
    protected $map;

    public function __construct(
        \Mirasvit\Report\Model\Config\Map $map,
        Filter $filter,
        TimezoneInterface $localeDate,
        ResolverInterface $localeResolver,
        array $data
    ) {
        $this->map = $map;

        parent::__construct($filter, $localeDate, $localeResolver, '', $data);
    }

    /**
     * Returns columns list
     *
     * @param UiComponentInterface $component
     * @return UiComponentInterface[]
     */
    protected function getColumns(UiComponentInterface $component)
    {
        if (!isset($this->columns[$component->getName()])) {
            $columns = $this->getColumnsComponent($component);
            foreach ($columns->getChildComponents() as $column) {
                if ($column->getData('config/add_field')) {
                    $this->columns[$component->getName()][$column->getName()] = $column;
                }
            }
        }

        return $this->columns[$component->getName()];
    }

    /**
     * @param DocumentInterface $document
     * @param array             $fields
     * @param array             $options
     * @return array
     */
    public function getRowData(DocumentInterface $document, $fields, $options)
    {
        $row = [];
        foreach ($fields as $field) {
            if (isset($options[$field])) {
                $key = $document->getCustomAttribute($field)->getValue();
                if (isset($options[$field][$key])) {
                    $row[] = $options[$field][$key];
                } else {
                    $row[] = '';
                }
            } else {
                $column = $this->map->getColumn($field);
                $row[] = $column->prepareValue($document->getCustomAttribute($field)->getValue(), true);
            }
        }


        return $row;
    }
}
