<?php
/**
 * @category    Fishpig
 * @package     FishPig/WordPress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
namespace Forix\FishPig\Model;



class Post extends \FishPig\WordPress\Model\Post
{

	public function getTermCollectionAsStringCategory($taxonomy)
	{

		$key = 'term_collection_as_string_' . $taxonomy;
		if (!$this->hasData($key)) {
			$string = array();
			$terms = $this->getTermCollection($taxonomy);

			$i=0; $showIncreate = false;
			$total = count($terms);
			if (count($terms) > 2) {
				$string[] = sprintf(' <li class="show-previous"  style="display: none;"><a title="show-previous" href="javascript:void(0);">+2</a></li>');
			}
			foreach($terms as $term) {
				$style = $class = "";
				$i++;
				if ($i > 2) {
					$style  = 'style="display:none;"';
					$showIncreate = true;

				} else {
					$class = "class='is_show'";
				}
				$string[] = sprintf(' <li data-count="'.$i.'" '.$class.' '.$style.'><a title="'.$term->getName().'" href="%s">%s</a></li>', $term->getUrl(), $term->getName());
			}

			$itemCount = count($string);

			if ($itemCount === 0) {
				$this->setData($key, false);
			}
			else if ($itemCount === 1) {
				$this->setData($key, $string[0]);
			}
			else {
				$lastItem = array_pop($string);
				$result = implode(' ', $string). $lastItem;
				if ($showIncreate) {
					if ($total > 2) {
						$t = $total-2;
					}
					$result.= " <li class='show-next'><a title='show category' class='show-cate' href='javascript:void(0);'>+$t</a></li>";
				}
				$this->setData($key, $result);
			}
		}

		return $this->_getData($key);
	}

	public function getTermCollectionAsStringCategoryFirstItem($taxonomy) {
		$key = 'term_collection_as_string_' . $taxonomy;
		if (!$this->hasData($key)) {
			$string = array();
			$terms = $this->getTermCollection($taxonomy);
			foreach($terms as $term) {
				$string[] = sprintf(' <li><a title="'.$term->getName().'" href="%s">%s</a></li>', $term->getUrl(), $term->getName());
			}

			$itemCount = count($string);

			if ($itemCount === 0) {
				$this->setData($key, false);
			}
			else if ($itemCount === 1) {
				$this->setData($key, $string[0]);
			}
			else {
				$lastItem = array_pop($string);
				$result = implode(' ', $string). $lastItem;
				$this->setData($key, $result);
			}
		}

		return $this->_getData($key);
	}

}
