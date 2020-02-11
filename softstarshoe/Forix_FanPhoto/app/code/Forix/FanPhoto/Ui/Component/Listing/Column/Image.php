<?php
/**
 * Created by PhpStorm.
 * User: dotung23@yahoo.com
 * Date: 3/6/2018
 * Time: 1:44 PM
 */

namespace Forix\FanPhoto\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Image
 */
class Image extends \Magento\Ui\Component\Listing\Columns\Column
{
	/**
	 * @param ContextInterface $context
	 * @param UiComponentFactory $uiComponentFactory
	 * @param array $components
	 * @param array $data
	 */
	public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		array $components = [],
		array $data = []
	) {
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}

	/**
	 * Prepare data source
	 *
	 * @param array $dataSource
	 * @return array
	 */
	public function prepareDataSource(array $dataSource)
	{
		foreach ($dataSource['data']['items'] as &$item) {
			$item['image_url'] = $this->prepareContent($item['image_url']);
		}
		return $dataSource;
	}

	/**
	 * Prepare content
	 *
	 * @param $priorityCode
	 * @return string
	 */
	protected function prepareContent($image_url)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$storeManager->getStore()->getBaseUrl();
		$mediaUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$imagePath = $mediaUrl.'fanphoto/';
		return
			"<img src='".$imagePath.$image_url."' width = '100px'/>";
	}
}
