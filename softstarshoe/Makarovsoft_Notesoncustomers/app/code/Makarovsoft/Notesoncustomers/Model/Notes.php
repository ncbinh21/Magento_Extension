<?php
/**
 * Copyright © 2015 Makarovsoft. All rights reserved.
 */

namespace Makarovsoft\Notesoncustomers\Model;

/**
 * Class Notes
 * @package Makarovsoft\Notesoncustomers\Model
 */
class Notes extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Makarovsoft\Notesoncustomers\Model\Resource\Notes');
    }

    public function getNotesCount($customerId)
    {
        return $this->getNotes($customerId)->count();
    }

    public function getNotes($customerId)
    {
        $collection = $this->getCollection()
            ->setOrder('updated_at', 'desc');

        $collection->addFieldToFilter('customer_id', $customerId);

        $collection->getSelect()->joinLeft(
            array('user'=>$collection->getTable('admin_user')),
            'user.user_id=main_table.user_id',
            array('firstname', 'lastname')
        );

        return $collection;
    }
}
