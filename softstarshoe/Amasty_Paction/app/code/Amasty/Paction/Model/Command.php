<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model;

use Amasty\Paction\Model;
use Magento\Framework\App\ResourceConnection;
class Command
{
    protected $_type       = '';
    protected $_info      = [];
    protected $_fieldLabel = '';
    
    protected $_errors    = array();

    public function __construct() {}

    public function getCreationData()
    {
        if (isset($this->_info)) {
            return $this->_info;
        }
        else{
            return false;
        }
    }

    /**
     * Gets list of not critical errors after the command execution
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;       
    }

    public function getLinkType()
    {
        $types = array(
            'copycrosssell' => \Magento\Catalog\Model\Product\Link::LINK_TYPE_CROSSSELL,
            'crosssell' => \Magento\Catalog\Model\Product\Link::LINK_TYPE_CROSSSELL,
            'uncrosssell' => \Magento\Catalog\Model\Product\Link::LINK_TYPE_CROSSSELL,
            'copyupsell'    => \Magento\Catalog\Model\Product\Link::LINK_TYPE_UPSELL,
            'upsell'    => \Magento\Catalog\Model\Product\Link::LINK_TYPE_UPSELL,
            'unupsell'    => \Magento\Catalog\Model\Product\Link::LINK_TYPE_UPSELL,
            'copyrelate'    => \Magento\Catalog\Model\Product\Link::LINK_TYPE_RELATED,
            'relate'    => \Magento\Catalog\Model\Product\Link::LINK_TYPE_RELATED,
            'unrelate'    => \Magento\Catalog\Model\Product\Link::LINK_TYPE_RELATED,
        );
        return $types[$this->_type];
    }
}
