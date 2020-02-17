<?php

namespace Forix\Widget\Block\Widget\RigModel;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\Json\DecoderInterface;
use Forix\Shopby\Helper\Data as HelperData;

class InputBox extends Template implements BlockInterface
{
	protected $_template = "rigmodel/input_box.phtml";
	protected $_jsonDecode;
	protected $_helper;

	public function __construct(
		Template\Context $context,
		DecoderInterface $jsonDecoder,
		HelperData $helperData

	)
	{
		parent::__construct($context, $data=[]);
		$this->_jsonDecode = $jsonDecoder;
		$this->_helper = $helperData;

	}

	public function getRigModel()
	{
	    // Hidro Remove
		$rigData = $this->_helper->getRigModelRegister();
		if ($rigData!="") {
			return $this->_jsonDecode->decode($rigData);
		}
		return [];
	}

    public function translitUrl($value){
        return $this->filterManager->translitUrl($value);
    }
}