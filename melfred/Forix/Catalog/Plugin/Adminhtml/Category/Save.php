<?php

namespace Forix\Catalog\Plugin\Adminhtml\Category;

class Save
{
	protected $_filesystem;
	protected $_file;

	public function __construct(
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Filesystem\Driver\File $file
	)
	{
		$this->_filesystem = $filesystem;
		$this->_file = $file;
	}

	public function afterExecute($subject, $result) {
		// Delete resize forder
		$source = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('resized/316/*');
		$files = glob($source);
		foreach($files as $file){
			if(is_file($file))
				unlink($file);
		}
		return $result;
	}
}
