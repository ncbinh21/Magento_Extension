<?php
namespace Forix\Media\Controller\Adminhtml\Video;

abstract class Product extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Forix_Media::item_list';


}