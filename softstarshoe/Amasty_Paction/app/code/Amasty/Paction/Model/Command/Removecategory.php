<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;

class Removecategory extends Addcategory
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ResourceConnection $resource
    )
    {
        parent::__construct($objectManager, $resource);
        $this->_type = 'removecategory';
        $this->_info = [
            'confirm_title'   => 'Remove Category',
            'confirm_message' => 'Are you sure you want to remove category?',
            'type'            => 'removecategory',
            'label'           => 'Remove Category',
            'fieldLabel'      => 'Category IDs',
            'placeholder'     => 'id1,id2,id3'
        ];
    }
}