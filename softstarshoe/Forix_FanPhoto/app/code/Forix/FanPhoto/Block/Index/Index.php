<?php


namespace Forix\FanPhoto\Block\Index;

class Index extends \Magento\Framework\View\Element\Template {
	const PAGE_SIZE = 20;
	protected $_photosFactory;
	protected $_resizeImage;
	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Forix\FanPhoto\Model\PhotoFactory $photosFactory
	 * @param array $data
	 */
	public function __construct(
		\Forix\Custom\Helper\ResizeImage $resizeImage,
		\Magento\Framework\View\Element\Template\Context $context,
		\Forix\FanPhoto\Model\PhotoFactory $photosFactory,
		array $data = []
	) {
		$this->_photosFactory = $photosFactory;
		$this->_resizeImage = $resizeImage;
		parent::__construct( $context, $data );
	}

	public function getPhotos() {
		//get values of current page
		$page = ( $this->getRequest()->getParam( 'p' ) ) ? $this->getRequest()->getParam( 'p' ) : 1;
		//get values of current limit
		//$pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 1;
		$pageSize         = self::PAGE_SIZE;
		$photosCollection = $this->_photosFactory->create()->getCollection();
		//$photosCollection->addFieldToFilter('is_active', 1);
		if($this->getRequest()->getParam( 'filterBy' ) != null && $this->getRequest()->getParam( 'filterBy' ) != 'All'){
			$photosCollection->addFieldToFilter('category_name', $this->getRequest()->getParam( 'filterBy' ));
		}
		$photosCollection->addFieldToFilter('is_active', 1);
		$photosCollection->setOrder( 'created_at', 'DESC' );
		$photosCollection->setPageSize( $pageSize );
		$photosCollection->setCurPage( $page );

		return $photosCollection;
	}

	/**
	 * @return $this
	 */
	protected function _prepareLayout() {
		parent::_prepareLayout();
		/** @var \Magento\Theme\Block\Html\Pager */
		$pager = $this->getLayout()->createBlock(
			'Magento\Theme\Block\Html\Pager'
		);
		$pager->setLimit( 20 )
		      ->setShowAmounts( false )
		      ->setCollection( $this->getPhotos() );
		$this->setChild( 'pager', $pager );
		$this->getPhotos()->load();

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPagerHtml() {
		return $this->getChildHtml( 'pager' );
	}

	function isFilterCurrent($filter) {
		if($filter == '' && !$this->getRequest()->getParam( 'filterBy' )) return true;
		return $this->getRequest()->getParam( 'filterBy' ) == $filter ? true : false;
	}

	function getPosition(){
		//get values of current page
		$page = ( $this->getRequest()->getParam( 'p' ) ) ? $this->getRequest()->getParam( 'p' ) : 1;
		return ($page - 1)*20;
	}

	public function getPhotosLimit() {
		$pageSize         = self::PAGE_SIZE * 7;
		$photosCollection = $this->_photosFactory->create()->getCollection();
		//$photosCollection->addFieldToFilter('is_active', 1);
		if($this->getRequest()->getParam( 'filterBy' ) != null && $this->getRequest()->getParam( 'filterBy' ) !="All"){
			$photosCollection->addFieldToFilter('category_name', $this->getRequest()->getParam( 'filterBy' ));
		}
		$photosCollection->addFieldToFilter('is_active', 1);
		$photosCollection->setOrder( 'created_at', 'DESC' );
		$photosCollection->setPageSize( $pageSize );

		return $photosCollection;
	}

    public function getUrlMediaPhoto()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

	public function resize($img_file, $widthCrop, $heightCrop = null, $crop = false)
	{
		return $this->_resizeImage->getThumbnailImage($img_file, $widthCrop, $heightCrop, $crop);
	}
}
