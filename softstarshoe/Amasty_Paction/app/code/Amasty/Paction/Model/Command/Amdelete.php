<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;

class Amdelete extends \Amasty\Paction\Model\Command
{
    /**
     * @var \Amasty\Paction\Helper\Data
     */
    protected $_helper;

    /**
     * @var ResourceConnection $resource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        ResourceConnection $resource
    ) {
        parent::__construct();
        $this->_helper = $helper;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();

        $this->_type = 'amdelete';
        $this->_info = [
            'confirm_title'   => 'Fast Delete',
            'confirm_message' => 'Are you sure you want to apply Fast Delete?',
            'type'            => $this->_type,
            'label'           => 'Fast Delete',
            'fieldLabel'      => ''
        ];
    }
        
    /**
     * Executes the command
     *
     * @param array $ids product ids
     * @param int $storeId store id
     * @param string $val field value
     * @return string success message if any
     */    
    public function execute($ids, $storeId, $val)
    {
        // we don't need to call parent as there are no values needed
        $this->_errors = [];

        if (!is_array($ids)) {
            throw new \Amasty\Paction\Model\CustomException(__('Please select product(s)'));
        }
        
        // do the bulk delete skiping all _before/_after delete observers
        // and indexing, as it cause thousands of queries in the
        // getProductParentsByChild function
        
        $db     = $this->connection;
        $table  = $this->resource->getTableName('catalog_product_entity');
        $entityIdName = $this->_helper->getEntityNameDependOnEdition();

        // foreign keys delete the rest
        $db->delete($table, $db->quoteInto($entityIdName . ' IN(?)', $ids));
        
        $success = __('Products have been successfully deleted. We recommend to refresh indexes at the System > Index Management page.');
        
        return $success; 
    }
}
