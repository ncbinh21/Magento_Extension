<?php
/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 8/9/17
 * Time: 5:21 PM
 */

namespace Forix\ImportHelper\Model\ResourceModel;


abstract class AbstractRawEntity
{
    /**
     * @var \Forix\ImportHelper\Model\Import\RawData
     */
    protected $_context;

    public function init($context){
        $this->_context = $context;
    }
    
    public abstract function getEntityTypeCode();
}