<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 19/05/2018
 * Time: 14:11
 */

namespace Magenest\SagepayUS\Controller\Adminhtml\Config;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;

class DownloadDebug extends \Magento\Backend\App\Action
{
    protected $directory_list;
    protected $fileFactory;

    public function __construct(
        Action\Context $context,
        DirectoryList $directory_list,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->directory_list = $directory_list;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $version = $this->getRequest()->getParam('version');
        $filename = "paya_debugfile_".$version."_".date("Ymd").".log";
        $file = $this->directory_list->getPath("var")."/log/sagepayus/debug.log";
        return $this->fileFactory->create($filename, @file_get_contents($file));
    }
}
