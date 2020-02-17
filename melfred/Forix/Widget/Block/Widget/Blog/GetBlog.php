<?php

namespace Forix\Widget\Block\Widget\Blog;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Forix\Widget\Helper\Data as helperData;

class GetBlog  extends Template implements BlockInterface
{
	protected $_template = "blog/get_blog.phtml";
	protected $_helper;

	public function __construct(
		Template\Context $context,
        helperData $helper,
		array $data = []
	)
	{
		parent::__construct($context, $data);
		$this->_helper = $helper;

	}

	public function getLastBlog()
	{
		$link     = $this->_helper->getConfigValue('blog/general/link');
		if ($link!="") {
			$outPut   = $this->_helper->getDataCurlLink($link);
			$rss      = $this->loadXmlString($outPut);
			$items    = $this->getItems($rss);
			return $items;
		}
		return [];
	}

	public function getTitle()
	{
		return $this->getData('text_title');
	}

	public function getShortDescription()
	{
		return $this->getData('short_desc');
	}

	protected function loadXmlString($result) {
		if ($result!="") {
			return simplexml_load_string($result);
		}
		return null;
	}

	protected function getItems($rss) {
		$getNums = $this->getData('num_record');
		$order   = $this->getData('order_by');
		$result = [];
		$i = 0;
		if ($rss!="") {
			foreach ($rss->channel->item as $item) {
				$i++;
				$result[] = [
					'title'       => (string)$item->title,
					'description' => (string)$item->description,
					'link'		  => (string)$item->link
				];
				if ($i == $getNums) {
					break;
				}
			}
		}

		if ($order == 'asc') {
			krsort($result);
		}

		return $result;
	}

}