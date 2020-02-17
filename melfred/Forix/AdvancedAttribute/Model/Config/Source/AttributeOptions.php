<?php

namespace Forix\AdvancedAttribute\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;

/**
 * Custom Attribute Renderer
 *
 * @author      Webkul Core Team <support@webkul.com>
 */
class AttributeOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{


    /**
     * @var $_attdata
     */
    protected $_attdata;

    public function __construct(
        \Forix\AdvancedAttribute\Model\AttributesCollection $AttributesCollection
    )
    {
        $this->_attdata = $AttributesCollection;
    }


    public function getAllOptions()
    {

        $items = $this->_attdata->loadData()->toArray()["items"];
        $tempdata = [['label' => 'Select Options', 'value' => '0']];
        foreach ($items as $item) {
            $tempdata[] = ['label' => $item["attribute_name"], 'value' => $item["attrid"]];
        }
        $this->_options = $tempdata;
        return $this->_options;
    }

    


}