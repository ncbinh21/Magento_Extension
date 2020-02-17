<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Configurable\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Modal;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;

/**
 * Data provider for Configurable panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConfigurablePanel extends \Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\ConfigurablePanel
{
	const GROUP_CONFIGURABLE = 'configurable';
	const ASSOCIATED_PRODUCT_MODAL = 'configurable_associated_product_modal';
	const ASSOCIATED_PRODUCT_LISTING = 'configurable_associated_product_listing';
	const CONFIGURABLE_MATRIX = 'configurable-matrix';

	/**
	 * @var string
	 */
	private static $groupContent = 'content';

	/**
	 * @var int
	 */
	private static $sortOrder = 30;

	/**
	 * @var UrlInterface
	 */
	private $urlBuilder;

	/**
	 * @var string
	 */
	private $formName;

	/**
	 * @var string
	 */
	private $dataScopeName;

	/**
	 * @var string
	 */
	private $dataSourceName;

	/**
	 * @var string
	 */
	private $associatedListingPrefix;

	/**
	 * @var LocatorInterface
	 */
	private $locator;

	/**
	 * @param LocatorInterface $locator
	 * @param UrlInterface $urlBuilder
	 * @param string $formName
	 * @param string $dataScopeName
	 * @param string $dataSourceName
	 * @param string $associatedListingPrefix
	 */
	public function __construct(
		LocatorInterface $locator,
		UrlInterface $urlBuilder,
		$formName,
		$dataScopeName,
		$dataSourceName,
		$associatedListingPrefix = ''
	) {
		parent::__construct($locator, $urlBuilder, $formName, $dataScopeName, $dataSourceName, $associatedListingPrefix);
		$this->locator = $locator;
		$this->urlBuilder = $urlBuilder;
		$this->formName = $formName;
		$this->dataScopeName = $dataScopeName;
		$this->dataSourceName = $dataSourceName;
		$this->associatedListingPrefix = $associatedListingPrefix;
	}

	/**
	 * Returns Dynamic rows records configuration
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function getRows()
	{
		return [
			'record' => [
				'arguments' => [
					'data' => [
						'config' => [
							'componentType' => Container::NAME,
							'isTemplate' => true,
							'is_collection' => true,
							'component' => 'Magento_Ui/js/dynamic-rows/record',
							'dataScope' => '',
						],
					],
				],
				'children' => [
					'thumbnail_image_container' => $this->getColumn(
						'thumbnail_image',
						__('Image'),
						[
							'fit' => true,
							'formElement' => 'fileUploader',
							'componentType' => 'fileUploader',
							'component' => 'Magento_ConfigurableProduct/js/components/file-uploader',
							'elementTmpl' => 'Magento_ConfigurableProduct/components/file-uploader',
							'fileInputName' => 'image',
							'isMultipleFiles' => false,
							'links' => [
								'thumbnailUrl' => '${$.provider}:${$.parentScope}.thumbnail_image',
								'thumbnail' => '${$.provider}:${$.parentScope}.thumbnail',
								'smallImage' => '${$.provider}:${$.parentScope}.small_image',
							],
							'uploaderConfig' => [
								'url' => $this->urlBuilder->addSessionParam()->getUrl(
									'catalog/product_gallery/upload'
								),
							],
							'dataScope' => 'image',
						],
						[
							'elementTmpl' => 'ui/dynamic-rows/cells/thumbnail',
							'fit' => true,
							'sortOrder' => 0
						]
					),
					'name_container' => $this->getColumn(
						'name',
						__('Name'),
						[],
						['dataScope' => 'product_link']
					),

					'rigmodel_container' => $this->getColumn(
						'mb_rig_model', __('Rig Model')
					),

					'sku_container' => $this->getColumn('sku', __('SKU')),
					'price_container' => $this->getColumn(
						'price',
						__('Price'),
						[
							'imports' => ['addbefore' => '${$.provider}:${$.parentScope}.price_currency'],
							'validation' => ['validate-zero-or-greater' => true]
						],
						['dataScope' => 'price_string']
					),
					'quantity_container' => $this->getColumn(
						'quantity',
						__('Quantity'),
						['dataScope' => 'qty'],
						['dataScope' => 'qty']
					),
					'price_weight' => $this->getColumn('weight', __('Weight')),
					'status' => [
						'arguments' => [
							'data' => [
								'config' => [
									'componentType' => 'text',
									'component' => 'Magento_Ui/js/form/element/abstract',
									'template' => 'Magento_ConfigurableProduct/components/cell-status',
									'label' => __('Status'),
									'dataScope' => 'status',
								],
							],
						],
					],
					'attributes' => [
						'arguments' => [
							'data' => [
								'config' => [
									'componentType' => Form\Field::NAME,
									'formElement' => Form\Element\Input::NAME,
									'component' => 'Magento_Ui/js/form/element/text',
									'elementTmpl' => 'ui/dynamic-rows/cells/text',
									'dataType' => Form\Element\DataType\Text::NAME,
									'label' => __('Attributes'),
								],
							],
						],
					],
					'actionsList' => [
						'arguments' => [
							'data' => [
								'config' => [
									'additionalClasses' => 'data-grid-actions-cell',
									'componentType' => 'text',
									'component' => 'Magento_Ui/js/form/element/abstract',
									'template' => 'Magento_ConfigurableProduct/components/actions-list',
									'label' => __('Actions'),
									'fit' => true,
									'dataScope' => 'status',
								],
							],
						],
					],
				],
			],
		];
	}

}
