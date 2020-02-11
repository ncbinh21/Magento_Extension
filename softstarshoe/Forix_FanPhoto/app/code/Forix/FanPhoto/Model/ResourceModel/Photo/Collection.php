<?php


namespace Forix\FanPhoto\Model\ResourceModel\Photo;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
	protected $_idFieldName = 'photo_id';

    protected function _construct()
    {
        $this->_init(
            'Forix\FanPhoto\Model\Photo',
            'Forix\FanPhoto\Model\ResourceModel\Photo'
        );
    }
}
