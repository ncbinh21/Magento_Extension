<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;


class Relate extends \Amasty\Paction\Model\Command
{
    /**
     * @var \Amasty\Paction\Helper\Data
     */
    protected $_helper;

    /**
     * @param ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper
     */
    protected $resource;

    protected $connection;

    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        ResourceConnection $resource
    )
    {
        parent::__construct();
        $this->_helper = $helper;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();

        $this->_type = 'relate';
        $this->_info = [
            'confirm_title'   => 'Relate',
            'confirm_message' => 'Are you sure you want to relate?',
            'type'            => $this->_type,
            'label'           => 'Relate',
            'fieldLabel'      => 'Selected To IDs',
            'placeholder'     => 'id1,id2,id3'
        ];

        $this->setFieldLabel();
    }

    
    public function setFieldLabel()
    {
        if ($this->isOneWay()) {
            if ( $this->_helper->getModuleConfig('links/' . $this->_type . '_reverse')) {
                $this->_info['fieldLabel'] = 'IDs to Selected'; // new option
            }
            else {
                $this->_info['fieldLabel'] = 'Selected To IDs'; // old option
            }
        }

        if ($this->isMultiWay()) {
            $this->_info['fieldLabel'] = '';
            $this->_info['hide_input'] = 1;
        }
    }
    
    public function isMultiWay()
    {
        return ($this->_helper->getModuleConfig('links/' . $this->_type) == 2);
    }  
    
    public function isTwoWay()
    {
       return ($this->_helper->getModuleConfig('links/' . $this->_type) == 1);
    }       

    public function isOneWay()
    {
       return ($this->_helper->getModuleConfig('links/' . $this->_type) == 0);
    }       

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

        if (!is_array($ids)) {
            throw new \Amasty\Paction\Model\CustomException(__('Please select product(s)'));
        }

        $vals = explode(',', $val);
        $num = 0;
            
        if ($this->isTwoWay()) {
            foreach ($vals as $mainId) {
                foreach ($ids as $id) {
                    $num += $this->_createNewLink($mainId, $id);
                    $num += $this->_createNewLink($id, $mainId);
                }
            }            
        } elseif ($this->isMultiWay()) {
            foreach ($ids as $id) {
                foreach ($ids as $id2) {
                    if ($id == $id2) {
                        continue;
                    }
                    $num += $this->_createNewLink($id, $id2);
                }
            }
        } else { // default one-way relation
            foreach ($vals as $mainId) {
                foreach ($ids as $id) {
                    if ($this->_helper->getModuleConfig('links/' . $this->_type . '_reverse')) {
                        $num += $this->_createNewLink($id, $mainId);
                    } else {
                        $num += $this->_createNewLink($mainId, $id);
                    }
                }
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
    
    //@todo optimize, move to one "insert into()  values (), (), .. ON DUPLICATE IGNORE"
    protected function _createNewLink($productId, $linkedProductId)
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
            $db->insert($table, array(
                'product_id'        => $productId,
                'linked_product_id' => $linkedProductId,
                'link_type_id'      => $this->getLinkType(),
            )); 
            $insertedCnt = 1;                   
        }
        
        return $insertedCnt;        
    }
}
