<?php

/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 8/10/17
 * Time: 3:10 PM
 */

namespace Forix\ImportHelper\Model\Export;
class Export
{
    protected $_entities = [];
    protected $_filesystem;
    protected $_rawData;
    protected $objectManager;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Forix\ImportHelper\Model\Export\RawData $rawData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $entities = []
    )
    {
        $this->_filesystem = $filesystem;
        $this->_entities = $entities;
        $this->_rawData = $rawData;
        $this->objectManager = $objectManager;
    }

    public function exports()
    {
        foreach ($this->_entities as $name => $model) {
            if ($model instanceof ExportRawInterface) {
                $csvfile = "importexport/" . $name . "_" . (date("Y-m-d")) . '.csv';
                $this->_rawData->setWriter(
                    $this->objectManager->create(
                        \Magento\ImportExport\Model\Export\Adapter\Csv::class,
                        ['fileSystem' => $this->_filesystem, 'destination' => $csvfile]
                    )
                )->setEntityModel($model);
                echo "\r\n Export To File: $csvfile \r\n";
                $this->_rawData->exports();
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('Please specify the model.' . get_class($model)));
            }
        }
    }
}