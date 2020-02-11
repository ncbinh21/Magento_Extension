<?php
/**
 * Copyright © 2015 Makarovsoft. All rights reserved.
 */

namespace Makarovsoft\Notesoncustomers\Model\Resource\Notes;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Makarovsoft\Notesoncustomers\Model\Notes', 'Makarovsoft\Notesoncustomers\Model\Resource\Notes');
    }
}
