<?php

namespace Forix\FishPig\Block\Post;

use \Magento\Framework\View\Element\Template\Context as MagentoContext;
use \FishPig\WordPress\Block\Context as WPContext;
use Magento\Framework\Registry;

class ListPost extends \FishPig\WordPress\Block\Post\ListPost {

	protected $_registry;

	public function __construct(
		MagentoContext $MagentoContext,
		WPContext $WPContext,
		Registry $registry
	)
	{
		parent::__construct($MagentoContext, $WPContext,$data=[]);
		$this->_registry = $registry;
	}

	protected function _toHtml() {
		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->setTemplate('Forix_FishPig::ajax/load_blog.phtml');
			die(parent::_toHtml());
		}

		if (!$this->getTemplate()) {
			$this->setTemplate('FishPig_WordPress::post/list.phtml');
		}

		return parent::_toHtml();

	}

	public function getPosts()
	{
		$itemFirst =  $this->getFirstPostItem();
		$firstId   =  $itemFirst->getData("ID");
		if ($this->_postCollection === null) {
			if ($this->getWrapperBlock()) {
				if ($this->_postCollection = $this->getWrapperBlock()->getPostCollection()) {
					if ($this->getPostType()) {
						$this->_postCollection->addPostTypeFilter($this->getPostType());
					}
				}
			} else {
				$this->_postCollection = $this->_factory->getFactory('Post')->create()->getCollection();
			}

			$this->_postCollection->getSelect()->where("ID != (?)", $firstId);

			if ($this->_postCollection && ($pager = $this->getChildBlock('pager'))) {
				$pager->setPostListBlock($this)->setCollection($this->_postCollection);
			}
		}

		return [
			'post_collection' => $this->_postCollection,
			'first_post'=>$itemFirst
		];
	}


	public function getFirstPostItem() {
		$term = $this->_registry->registry("wordpress_term");
		if ($term == null) {
			$postCollection  =  $this->_factory->getFactory('Post')->create()->getCollection();
			$postCollection->getSelect()
				->where("main_table.post_type = (?)","post")
				->where("main_table.post_status IN (?)", ["publish","protected"])
				->order("main_table.post_date DESC");
			return $postCollection->getFirstItem();
		} else {
			$termId = $term->getData("term_id");
			$postCollection  =  $this->_factory->getFactory('Post')->create()->getCollection();
			$postCollection
				->getSelect()->joinLeft(["wp_term"=>"wp_term_relationships"],"wp_term.object_id = main_table.ID","")
				->where("wp_term.term_taxonomy_id = (?)", $termId)
				->where("main_table.post_type = (?)","post")
				->where("main_table.post_status IN (?)", ["publish","protected"])
				->order("main_table.post_date DESC");
			return $postCollection->getFirstItem();
		}



	}
}