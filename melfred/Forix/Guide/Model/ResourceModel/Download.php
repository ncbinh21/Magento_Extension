<?php


namespace Forix\Guide\Model\ResourceModel;

class Download extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_guide_download', 'download_id');
    }
}
