<?php

/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 7/31/17
 * Time: 4:26 PM
 */

namespace Forix\ImportHelper\Model\ResourceModel;

class RawData extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_rawdata', 'rawdata_id');
    }

    public function renderColumn($columns)
    {
        foreach ($columns as $column) {
            $column = strtolower(trim($column));
            if($column) {
                if (!($this->getConnection()->tableColumnExists($this->getMainTable(), $column))) {
                    $this->getConnection()->addColumn($this->getMainTable(), $column,
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'nullable' => true,
                            'comment' => $column,
                        ]);
                }
            }
        }
    }

    /**
     * Return behavior from import data table.
     *
     * @return string
     */
    public function getBehavior()
    {
        return $this->getUniqueColumnData('behavior');
    }

    /**
     * Return entity type code from import data table.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return $this->getUniqueColumnData('entity');
    }

    /**
     * Return request data from import data table
     *
     * @param string $code parameter name
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getUniqueColumnData($code)
    {
        $connection = $this->getConnection();
        $values = array_unique($connection->fetchCol($connection->select()->from($this->getMainTable(), [$code])));

        if (count($values) != 1) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Error in data structure: %1 values are mixed', $code)
            );
        }
        return $values[0];
    }

    public function saveData($data)
    {
        $this->getConnection()->insert($this->getMainTable(), $data);
    }
}