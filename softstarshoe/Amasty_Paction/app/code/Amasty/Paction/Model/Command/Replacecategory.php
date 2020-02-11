<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;

class Replacecategory extends Addcategory
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ResourceConnection $resource
    )
    {
        parent::__construct($objectManager, $resource);
        $this->_type = 'replacecategory';
        $this->_info = [
            'confirm_title'   => 'Replace Category',
            'confirm_message' => 'Are you sure you want to replace category?',
            'type'            => 'replacecategory',
            'label'           => 'Replace Category',
            'fieldLabel'      => 'Category IDs',
            'placeholder'     => 'id1,id2,id3'
        ];
    }
}