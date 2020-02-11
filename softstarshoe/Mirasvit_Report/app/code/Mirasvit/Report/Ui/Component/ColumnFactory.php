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


namespace Mirasvit\Report\Ui\Component;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\ObjectManagerInterface;

class ColumnFactory
{
    /**
     * @var \Magento\Framework\View\Element\UiComponentFactory
     */
    protected $componentFactory;

    /**
     * @var array
     */
    protected $jsComponentMap = [
        'text'    => 'Mirasvit_Report/js/grid/columns/column',
        'select'  => 'Magento_Ui/js/grid/columns/select',
        'date'    => 'Magento_Ui/js/grid/columns/date',
        'number'  => 'Mirasvit_Report/js/grid/columns/number',
        'price'   => 'Mirasvit_Report/js/grid/columns/price',
        'country' => 'Mirasvit_Report/js/grid/columns/country',
        'html'    => 'Mirasvit_Report/js/grid/columns/html',
    ];

    /**
     * @var array
     */
    protected $dataTypeMap = [
        'default'     => 'text',
        'text'        => 'text',
        'html'        => 'text',
        'boolean'     => 'select',
        'select'      => 'select',
        'multiselect' => 'select',
        'date'        => 'date',
        'number'      => 'text',
        'price'       => 'text',
        'country'     => 'select',
    ];

    /**
     * ColumnFactory constructor.
     *
     * @param UiComponentFactory     $componentFactory
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        UiComponentFactory $componentFactory,
        ObjectManagerInterface $objectManager
    ) {
        $this->componentFactory = $componentFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function create($attribute, $context, array $config = [])
    {
        $config = array_merge([
            'dataType'  => $this->getDataType($config['type']),
            'valueType' => $config['type'],
            'component' => $this->getJsComponent($config['type']),
            'align'     => 'left',
        ], $config);

        if ($config['options'] && is_string($config['options'])) {
            $config['options'] = $this->objectManager->get($config['options'])->getAllOptions();
        }

        $arguments = [
            'data'    => [
                'config' => $config,
            ],
            'context' => $context,
        ];

        return $this->componentFactory->create($attribute, 'column', $arguments);
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getJsComponent($type)
    {
        return isset($this->jsComponentMap[$type])
            ? $this->jsComponentMap[$type]
            : $this->jsComponentMap['text'];
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getDataType($type)
    {
        return isset($this->dataTypeMap[$type])
            ? $this->dataTypeMap[$type]
            : $this->dataTypeMap['default'];
    }
}
