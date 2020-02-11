<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

namespace FishPig\WordPress\Block\Homepage;

class View extends \FishPig\WordPress\Block\Post\PostList\Wrapper\AbstractWrapper
{
	/*
	 *
	 * @return
	 */
	public function getEntity()
	{
		if (!$this->hasEntity()) {
			if ($homepage = $this->_registry->registry('wordpress_homepage')) {
				$this->setData('entity', $homepage->getBlogPage() ? $homepage->getBlogPage() : $homepage);
			}
			else {
				$this->setData('entity', false);
			}
		}

		return $this->getData('entity');
	}

	/*
	 * Retrieve the tag line set in the WordPress Admin
	 *
	 * @return string
	 */
	public function getIntroText()
	{
		return trim($this->getEntity()->getContent());
	}

	/*
	 * Returns the blog homepage URL
	 *
	 * @return string
	 */
	public function getBlogHomepageUrl()
	{
		return $this->getEntity()->getUrl();
	}

	/*
	 * Determine whether the first page of posts are being displayed
	 *
	 * @return bool
	 */
	public function isFirstPage()
	{
		return $this->getRequest()->getParam('page', '1') === '1';
	}

	/*
	 * Generates and returns the collection of posts
	 *
	 * @return
	 */
	protected function _getPostCollection()
	{
		//return parent::_getPostCollection()->addStickyPostsToCollection()->addPostTypeFilter('post');
		return parent::_getPostCollection()->addPostTypeFilter('post');
	}

	/*
	 * Retrieve the tag line set in the WordPress Admin
	 *
	 * @return string
	 */
	public function getPageTitle()
	{
		return trim($this->getEntity()->getPageTitle());
	}

	/*
	 *
	 * @return
	 */
	public function getPostContactUs()
	{
		$collection = parent::_getPostCollection()->addPostTypeFilter('post');
		$collection->addFieldToFilter('post_title',['like' => '%About Softstar%']);

		return $collection->getFirstItem();
	}

	/*
	 *
	 * @return
	 */
	public function getFeaturedPosts()
	{
		$collection = $this->_factory->getFactory('Post')->create()->getCollection();
		$collection->addIsStickyPostFilter()->addPostTypeFilter('post');
		$collection
			->setPageSize(3)
			->setCurPage(1);
		$collection->load();
		//echo $collection->getSelect();
		return $collection;
	}
}
