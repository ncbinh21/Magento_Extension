<?php
/**
 * Hidro Forix Webdesign. 
 * Copyright (C) 2017  Hidro Le
 * 
 * This file included in Forix/QuoteLetter is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Forix\QuoteLetter\Model\QuoteLetter;

use Forix\QuoteLetter\Model\ResourceModel\QuoteLetter\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $loadedData;
    protected $collection;

    protected $dataPersistor;
    /**
     * @var PoolInterface 
     */
    protected $pool;

    protected $_registry;
    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        \Magento\Framework\Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->dataPersistor = $dataPersistor;
        $this->collection = $collectionFactory->create();
        $this->pool = $pool;
        $this->_registry = $registry;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData()
    {
        
        
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $regis = $this->_registry->registry('forix_quoteletter_quoteletter');
        if (!empty($regis)) {
            $this->data[$regis->getId()] = $regis->getData();
        }
        
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }
        return $this->data;
    }


    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta['assign_category'] = [
            'children' => [
                'container_category_ids' =>[
                    'children' => [
                        'category_ids' => [
                            'arguments' => [
                                'data' => [
                                    'config' =>[
                                        'dataType' => 'text',
                                        'formElement' => 'input',
                                        'visible' => '1',
                                        'required' => '0',
                                        'notice' => null,
                                        'default' => null,
                                        'label' => __('Categories'),
                                        'code' => 'category_ids',
                                        'source' => 'QuoteLetter',
                                        'componentType' => 'field',
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'arguments' => [
                        'data' => [
                            'config' =>[
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'label' => __('Categories'),
                                'breakLine'=> false,
                                'required' => 0
                            ]
                        ]
                    ]
                ]
            ],
            'arguments' => [
                'data' => [
                    'config' =>[
                        'componentType' => 'fieldset',
                        'label' => __('Assign To Category'),
                        'collapsible' => true,
                        'sortOrder' => 30
                    ]
                ]
            ]
        ];
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
