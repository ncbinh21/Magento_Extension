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


namespace Mirasvit\Report\Model\Config;

use Mirasvit\Report\Model\Config\Map\Converter;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;

class Map
{
    /**
     * @var Map\Data
     */
    private $data;

    /**
     * @var MapRepositoryInterface
     */
    private $mapRepository;

    public function __construct(
        MapRepositoryInterface $mapRepository,
        Map\Data $data
    ) {
        $this->mapRepository = $mapRepository;
        $this->data = $data;
    }

    /**
     * @return $this
     */
    public function load()
    {
        \Magento\Framework\Profiler::start(__METHOD__);
        $config = $this->data->get('config');


        if (is_array($config['table'])) {
            foreach ($config['table'] as $data) {
                $this->initTable($data);
            }
        }

        if (is_array($config['eavTable'])) {
            foreach ($config['eavTable'] as $data) {
                $this->initEavTable($data);
            }
        }

        if (is_array($config['relation'])) {
            foreach ($config['relation'] as $data) {
                $this->initRelation($data);
            }
        }

        foreach ($config['table'] as $data) {
            $this->initColumns($data);
        }

        foreach ($config['eavTable'] as $data) {
            $this->initColumns($data);
        }
        \Magento\Framework\Profiler::stop(__METHOD__);

        return $this;
    }

    /**
     * @param array $data
     * @return void
     */
    private function initTable($data)
    {
        $this->mapRepository->createTable($data[Converter::DATA_ATTRIBUTES_KEY]);
    }

    /**
     * @param array $data
     * @return void
     */
    private function initEavTable($data)
    {
        $this->mapRepository->createEavTable($data[Converter::DATA_ATTRIBUTES_KEY]);
    }

    /**
     * @param array $data
     * @return void
     */
    private function initRelation($data)
    {
        $data = $data[Converter::DATA_ARGUMENTS_KEY];

        $data['leftTable'] = $this->mapRepository->getTable($data['leftTable']);
        $data['rightTable'] = $this->mapRepository->getTable($data['rightTable']);

        $this->mapRepository->createRelation($data);
    }

    /**
     * @param array $data
     * @return void
     */
    private function initColumns($data)
    {
        $table = $this->mapRepository->getTable($data[Converter::DATA_ATTRIBUTES_KEY]['name']);

        if (isset($data['columns'])) {
            foreach ($data['columns']['column'] as $data) {
                $data[Converter::DATA_ATTRIBUTES_KEY]['table'] = $table;
                if (isset($data[Converter::DATA_ATTRIBUTES_KEY]['tables'])) {
                    $tables = explode(',', $data[Converter::DATA_ATTRIBUTES_KEY]['tables']);
                    foreach ($tables as $idx => $tbl) {
                        $tables[$idx] = $this->mapRepository->getTable($tbl);
                    }

                    $data[Converter::DATA_ATTRIBUTES_KEY]['tables'] = $tables;
                } else {
                    $data[Converter::DATA_ATTRIBUTES_KEY]['tables'] = [];
                }
                $this->initColumn($data);
            }
        }
    }

    /**
     * @param array $data
     * @return void
     */
    private function initColumn($data)
    {
        $data = $data[Converter::DATA_ATTRIBUTES_KEY];
        $data['fields'] = array_map('trim', explode(',', $data['fields']));

        $name = $data['name'];

        $objectData = [
            'name' => $name,
            'data' => $data,
        ];

        if (isset($data['aggregations'])) {
            $data['aggregations'] = explode(',', $data['aggregations']);
            foreach ($data['aggregations'] as $type) {
                $class = '\Mirasvit\Report\Model\Query\Column\\' . ucfirst($type);

                $this->mapRepository->createColumn($objectData, $class);
            }
        } else {
            if (isset($data['class'])) {
                $class = $data['class'];

                $this->mapRepository->createColumn($objectData, $class);
            } else {
                $this->mapRepository->createColumn($objectData);
            }
        }
    }
}
