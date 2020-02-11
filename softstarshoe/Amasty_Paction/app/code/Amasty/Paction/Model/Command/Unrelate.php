<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;

class Unrelate extends \Amasty\Paction\Model\Command
{
    protected $resource;
    protected $connection;

    public function __construct(
        ResourceConnection $resource
    ) {
        parent::__construct();
        $this->resource = $resource;
        $this->connection = $resource->getConnection();

        $this->_type = 'unrelate';
        $this->_info = [
            'confirm_title'   => 'Remove Relations',
            'confirm_message' => 'Are you sure you want to remove relations?',
            'type'            => $this->_type,
            'label'           => 'Remove Relations',
            'fieldLabel'      => 'Select Algorithm'
        ];
    }

    /**
     * Executes the command
     * @param array $ids product ids
     * @param int $storeId store id
     * @param string $val field value
     *
     * @throws Exception
     * @return string success message if any
     */
    public function execute($ids, $storeId, $val)
    {
        $this->_errors = [];

        if (!is_array($ids)) {
            throw new \Amasty\Paction\Model\CustomException(__('Please select product(s)'));
        }
        
        $db    = $this->connection;
        $table = $this->resource->getTableName('catalog_product_link');
        
        if (1 == $val) { // between selected
            $where = array(
                'product_id IN(?)'        => $ids,
                'linked_product_id IN(?)' => $ids,
            );
        } elseif (2 == $val) { // selected products from all
            $where = array(
                'linked_product_id IN(?)' => $ids,
            );
        } else { // Remove all relations from selected products
            $where = array(
                'product_id IN(?)' => $ids,
            );
        }
        
        $db->delete($table, array_merge($where, array('link_type_id = ?' => $this->getLinkType())));
        
        $success = __('Product associations have been successfully deleted.');
        
        return $success;
    }
}
