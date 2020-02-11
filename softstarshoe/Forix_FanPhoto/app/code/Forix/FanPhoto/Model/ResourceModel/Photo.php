<?php


namespace Forix\FanPhoto\Model\ResourceModel;

class Photo extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_fanphoto_photo', 'photo_id');
    }
}
