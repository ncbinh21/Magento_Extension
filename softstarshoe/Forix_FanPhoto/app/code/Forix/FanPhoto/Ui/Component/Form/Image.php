<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Forix\FanPhoto\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;

/**
 * Class CreatedAt
 *
 * @package Aheadworks\Giftcard\Ui\Component\Form\Giftcard
 */
class Image extends \Magento\Ui\Component\Form\Field
{

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UiComponentInterface[] $components
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
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
	    if (isset($dataSource['data']['image_url']) && $dataSource['data']['image_url']) {
		    try {
			    $image_url     = $dataSource['data']['image_url'];
			    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			    $storeManager  = $objectManager->get( '\Magento\Store\Model\StoreManagerInterface' );
			    $storeManager->getStore()->getBaseUrl();
			    $mediaUrl = $storeManager->getStore()->getBaseUrl( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
			    $imagePath = $mediaUrl . 'fanphoto/';
			    $dataSource['data']['image_url'] = "<img src='" . $imagePath . $image_url . "' />";
		    } catch ( \Exception $e ) {
			    $dataSource = '';
		    }

		    return $dataSource;
	    }

    }
}
