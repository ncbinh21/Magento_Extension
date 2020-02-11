<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;


class Copyrelate extends \Amasty\Paction\Model\Command
{
    /**
     * @var \Amasty\Paction\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection $resource
     */
    protected $resource;


    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        ResourceConnection $resource
    ) {
        parent::__construct();
        $this->_helper = $helper;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();

        $this->_type = 'copyrelate';
        $this->_info = [
            'confirm_title'   => 'Copy Relations',
            'confirm_message' => 'Are you sure you want to copy relations?',
            'type'            => $this->_type,
            'label'           => 'Copy Relations',
            'fieldLabel'      => 'From'
        ];
    }

    protected $_link_attribute_id = null;
    protected $_link_attribute_data_type = null;

    /**
     * Executes the command
     *
     * @param array $ids product ids
     * @param int $storeId store id
     * @param string $val field value
     * @throws Exception
     * @return string success message if any
     */    
    public function execute($ids, $storeId, $val)
    {
        $success = '';
        $fromId = intVal(trim($val));

        if (!$fromId) {
            throw new \Amasty\Paction\Model\CustomException(__('Please provide a valid product ID'));
        }

        if (in_array($fromId, $ids)) {
            throw new \Amasty\Paction\Model\CustomException(__('Please remove source product from the selected products'));
        }

        $records = $this->_getRelations($fromId);

        if (empty($records)) {
            throw new \Amasty\Paction\Model\CustomException(__('Source product has no relations'));
        }

        $num = 0;
        foreach ($ids as $id) {
            foreach ($records as $record) {
                if ($id == $record['linked_product_id']) {
                    continue;
                }
                $num += $this->_createNewLink($id, $record['linked_product_id'], $record['link_id']);
            }
        }

        if ($num) {
            if (1 == $num)
                $success = __('Product association has been successfully added.');
            else {
                $success = __('%1 product associations have been successfully added.', $num);
            }
        }
        
        return $success; 
    }

    protected function _getRelations($productId)
    {
        $db     = $this->connection;
        $table  = $this->resource->getTableName('catalog_product_link');

        $select = $db->select()->from($table)
            ->where('link_type_id=?', $this->getLinkType())
            ->where('product_id =?', $productId);

        $rows = $db->fetchAll($select);

        return $rows;
    }

    protected function _getLinkAttributeId($code = 'position')
    {
        if (is_null($this->_link_attribute_id)) {
            $db    = $this->connection;
            $table = $this->resource->getTableName('catalog_product_link_attribute');

            $select = $db->select()->from($table)
                ->where('link_type_id=?', $this->getLinkType())
                ->where('product_link_attribute_code=?', $code);
            $row = $db->fetchRow($select);

            $this->_link_attribute_id = $row['product_link_attribute_id'];
            $this->_link_attribute_data_type = $row['data_type'];
        }
        return $this->_link_attribute_id;
    }
    
    protected function _createNewLink($productId, $linkedProductId, $parentLinkId)
    {
        $db     = $this->connection;
        $table  = $this->resource->getTableName('catalog_product_link');

        $select = $db->select()->from($table)
            ->where('link_type_id=?', $this->getLinkType())
            ->where('product_id =?', $productId)
            ->where('linked_product_id =?', $linkedProductId);
        $row = $db->fetchRow($select);

        $insertedCnt = 0;
        if (!$row) {
            $insertedCnt = $db->insert($table, array(
                'product_id'        => $productId,
                'linked_product_id' => $linkedProductId,
                'link_type_id'      => $this->getLinkType(),
            ));
            $newLinkId = $db->lastInsertId();

            $linkAttributeId = $this->_getLinkAttributeId();
            $table = $this->resource->getTableName('catalog_product_link_attribute_' . $this->_link_attribute_data_type);
            $select = $db->select()->from($table)
                ->where('link_id=?', $parentLinkId)
                ->where('product_link_attribute_id=?', $linkAttributeId);
            $row = $db->fetchRow($select);
            
            if ($row) {
                $db->insert($table, array(
                    'product_link_attribute_id' => $linkAttributeId,
                    'link_id' => $newLinkId,
                    'value'   => $row['value'],
                ));
            }
        }

        return $insertedCnt;
    } 
}
