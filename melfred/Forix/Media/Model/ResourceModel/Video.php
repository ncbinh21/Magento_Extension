<?php


namespace Forix\Media\Model\ResourceModel;

class Video extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_media_video', 'video_id');
    }
}
