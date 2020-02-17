<?php
namespace Forix\AdvancedAttribute\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	protected $_optionCollection;
	protected $_advancedImage;

	public function __construct(
		Context $context,
		\Forix\AdvancedAttribute\Model\ResourceModel\Option\CollectionFactory $optionCollection,
		\Forix\AdvancedAttribute\Model\Image $image
	)
	{
		parent::__construct($context);
		$this->_optionCollection = $optionCollection;
		$this->_advancedImage = $image;
	}

	public function getCurrentGroundInfo($gid="")
	{
		if ($gid!="") {
			$data = $this->_optionCollection->create()->addOptionToFilter($gid);
			if ($data) {
				return  $data->getData()[0];
			}
		}
		return false;
	}

	public function readImageContent($path)
	{
		if (substr($path,strrpos($path,".")+1) == "svg") {
			$filename = $this->_advancedImage->getBaseDir() . $path;
			if (file_exists($filename)) {
				return file_get_contents($filename);
			}
		}
		return '';
	}




}