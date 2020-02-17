<?php

namespace Forix\Product\Model;

class GroundIndex

{
    protected $setup;
    protected $attOptions;
    protected $_productCollectionFactory;
    protected $_helper;


    CONST TABLENAME = 'ground_condition_weight';
    CONST COLUMN_PREFIX = 'w_';

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Forix\Product\Model\Source\GroundCondition $attOptions,
        \Magento\Framework\Module\Setup $setup,
        \Forix\Product\Helper\Data $data
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->setup = $setup;
        $this->attOptions = $attOptions;
        $this->_helper = $data;

    }


    public function updateRow($data)
    {
        /*
         * insert data to index
         */
        if (is_array($data)) {
            $this->setup->getConnection()->insertOnDuplicate(
                self::TABLENAME, $data
            );
        }
    }

    public function rebuiltIndex()
    {
        $options = $this->attOptions->getAllOptions(false);
        $setup = $this->setup->getConnection();
        //drop table
        $setup->dropTable(self::TABLENAME);

        //rebuilt table
        $table = $setup->newTable(self::TABLENAME)
            ->addColumn(
                'pid',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Product ID'
            );

        foreach ($options as $option) {
                $table->addColumn(
                    self::COLUMN_PREFIX . $option['value'],
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => true],
                    $option['label']
                );
        }
        $setup->createTable($table);
        $this->processProductCollection();
    }

    public function processProductCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect([
            'mb_soil_type_best', 'mb_soil_type_better', 'mb_soil_type_good'
        ], true);
        /**
         * @var $product \Magento\Catalog\Model\Product
         */
        foreach ($collection as $product) {
//            TODO : save data to mb_ground_condition?
//            ref: https://framework.zend.com/manual/1.5/en/zend.db.table.rowset.html
//            $row->save();
            $data = $this->processData($product->getEntityId(), $product->getData('mb_soil_type_best'), $product->getData('mb_soil_type_better'), $product->getData('mb_soil_type_good'));
            $this->updateRow($data["indexdata"]);

            if (is_array($data['condition'])) {
                /**
                 * @var $resource \Magento\Catalog\Model\ResourceModel\Product
                 */
                $resource = $product->getResource();
                $mb_ground_condition = array_filter($data['condition'], function ($value) {
                    return $value === '0' || !empty($value);
                });
                $product->setData('mb_ground_condition', implode(',', $mb_ground_condition));
                $resource->saveAttribute($product, 'mb_ground_condition');
            }

        }

        return true;
    }

    public function getEmptyRow()
    {
        $sData["pid"] = '0';
        $options = $this->attOptions->getAllOptions(false);
        foreach ($options as $option) {
                $sData[self::COLUMN_PREFIX . $option['value']] = '0';
        }
        return $sData;
    }

    public function getColPrefix()
    {
        return self::COLUMN_PREFIX;
    }

    public function processData($pid, $best, $better, $good)
    {
        $typearray["mb_soil_type_best"] = is_array($best)?$best:explode(',', $best);
        $typearray["mb_soil_type_better"] = is_array($better)?$better:explode(',', $better);
        $typearray["mb_soil_type_good"] = is_array($good)?$good:explode(',', $good);
        $dataRow = $this->getEmptyRow();
        $dataRow['pid'] = $pid;

        $tempArray = [
            'mb_soil_type_best' => '',
            'mb_soil_type_better' => '',
            'mb_soil_type_good' => ''
        ];

        $groundMap = json_decode($this->_helper->getGroundConditionConfig(), true);
        if ($groundMap) {
            //loop condition map
            foreach ($groundMap as $groundItem) {
                //loop good
                foreach ($typearray["mb_soil_type_good"] as $item) {
                    if ($groundItem["mb_soil_type_good"] == $item) {
                        $tempArray["mb_soil_type_good"] .= $groundItem["mb_ground_condition"] . ',';
                        $dataRow[self::COLUMN_PREFIX . $groundItem["mb_ground_condition"]] = 1;
                    }
                }
                //loop better
                foreach ($typearray["mb_soil_type_better"] as $item) {
                    if ($groundItem["mb_soil_type_better"] == $item) {
                        $tempArray["mb_soil_type_better"] .= $groundItem["mb_ground_condition"] . ',';
                        $dataRow[self::COLUMN_PREFIX . $groundItem["mb_ground_condition"]] = 3;
                    }
                }
                //loop best
                foreach ($typearray["mb_soil_type_best"] as $item) {
                    if ($groundItem["mb_soil_type_best"] == $item) {
                        $tempArray["mb_soil_type_best"] .= $groundItem["mb_ground_condition"] . ',';
                        $dataRow[self::COLUMN_PREFIX . $groundItem["mb_ground_condition"]] = 9;
                    }
                }
            }
        }
        $saveCondition = array();
        $saveCondition = array_merge($saveCondition, explode(',', $tempArray["mb_soil_type_best"]));
        $saveCondition = array_merge($saveCondition, explode(',', $tempArray["mb_soil_type_better"]));
        $saveCondition = array_merge($saveCondition, explode(',', $tempArray["mb_soil_type_good"]));
        $saveCondition = array_unique(array_filter($saveCondition));
        return ["condition" => $saveCondition,
                "indexdata" => $dataRow];
    }
}