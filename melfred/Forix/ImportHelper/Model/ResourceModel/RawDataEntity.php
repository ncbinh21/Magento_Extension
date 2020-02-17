<?php
/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 8/3/17
 * Time: 4:59 PM
 */

namespace Forix\ImportHelper\Model\ResourceModel;


class RawDataEntity extends AbstractRawEntity
{   
    protected $_resource;
    
    protected $_data;
    
    protected $_cols;
    
    protected $_hasRenderColumn = false;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    
    public function getEntityTypeCode(){
        return 'rawdata';
    }
    
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
        $this->_logger = $logger;
    }
    
    public function addColumnHeader($cols){
        if(is_array($cols)) {
            foreach ($cols as $col) {
                $this->_cols[] = $col;
                $this->_hasRenderColumn = false;
            }
        }else if($cols){
            $this->_cols[] = $cols;
            $this->_hasRenderColumn = false;
        }
    }
    
    public function addRowData($rowData){        
        $this->_data[] = $rowData;
        if(1000 == count($this->_data)){
            $this->saveData();
        }
        return $this;
    }

    public function saveData(){
        if(count($this->_data)) {
            if(!$this->_hasRenderColumn) {
                $this->_context->retrieveEntityTypeByName($this->getEntityTypeCode())->getResource()->renderColumn($this->_cols);
                $this->_hasRenderColumn = true;
            }
            $connection = $this->_resource->getConnection();
            $connection->beginTransaction();
            try {
                $connection->insertMultiple($this->_context->retrieveEntityTypeByName($this->getEntityTypeCode())->getResource()->getMainTable(), $this->_data);
            }catch (\Exception $e){
                $this->_logger->debug(var_export($this->_data, true));
                echo "\r\n".$e->getMessage() . "\r\n";
            }
            $connection->commit();
        }
        $this->_data = [];
    }
}