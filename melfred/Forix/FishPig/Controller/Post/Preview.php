<?php
/**
 * @
**/
namespace Forix\FishPig\Controller\Post;

use FishPig\WordPress\Model\PostFactory;

class Preview extends \FishPig\WordPress\Controller\Post\Preview
{

	protected $postFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Registry $registry,
		\FishPig\WordPress\Model\App $app,
		\FishPig\WordPress\Model\App\Factory $factory,
		PostFactory $postFactory
	) {
		parent::__construct($context,$registry,$app,$factory);
		$this->postFactory = $postFactory;
	}

	/**
	 * Load and return a Post model
	 *
	 * @return \FishPig\WordPress\Model\Post|false 
	**/
    protected function _getEntity()
    {
    	$post = $this->postFactory->create()->load(
	    	$this->getRequest()->getParam('preview_id')
	    );
	    
	    if ($revision = $post->getLatestRevision()) {
		    return $revision;
	    }

		return $post->getId() ? $post : false;
    }

}
