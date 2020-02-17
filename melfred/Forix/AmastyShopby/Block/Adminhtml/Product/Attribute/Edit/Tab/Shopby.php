<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Forix\AmastyShopby\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Amasty\Shopby\Block\Adminhtml\Product\Attribute\Edit\Tab\Shopby\Multiselect;
use Amasty\Shopby\Helper\Category;
use Amasty\ShopbyBase\Block\Widget\Form\Element\Dependence;
use Amasty\Shopby\Helper\FilterSetting as FilterSettingHelper;
use Amasty\ShopbyBase\Model\FilterSetting;
use Amasty\ShopbyBase\Model\FilterSettingFactory;
use Amasty\Shopby\Model\Source\VisibleInCategory;
use Amasty\Shopby\Model\Source\Category as CategorySource;
use Amasty\Shopby\Model\Source\Attribute as AttributeSource;
use Amasty\Shopby\Model\Source\Attribute\Option as AttributeOptionSource;
use Amasty\Shopby\Model\Source\DisplayMode;
use Amasty\Shopby\Model\Source\MeasureUnit;
use Amasty\Shopby\Model\Source\MultipleValuesLogic;
use Amasty\Shopby\Model\Source\ShowProductQuantities;
use Amasty\Shopby\Model\Source\CategoryTreeDisplayMode;
use Amasty\Shopby\Model\Source\SortOptionsBy;
use Amasty\ShopbySeo\Model\Source\RelNofollow;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Catalog\Model\Entity\Attribute;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\Data\Form\Element\Fieldset;
use Amasty\Shopby\Model\Source\FilterPlacedBlock;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Shopby extends \Amasty\Shopby\Block\Adminhtml\Product\Attribute\Edit\Tab\Shopby
{
	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function _prepareForm()
	{

		/** @var \Magento\Framework\Data\Form $form */
		$form = $this->_formFactory->create(
			['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
		);

		$this->prepareFilterSetting();
		$form->setDataObject($this->setting);

		$form->addField(
			'filter_code',
			'hidden',
			[
				'name' => 'filter_code',
				'value' => $this->setting->getFilterCode(),
			]
		);

		$yesNoSource = $this->yesNo->toOptionArray();
		/** @var  $dependence Dependence */
		$dependence = $this->getLayout()->createBlock(
			Dependence::class
		);

		$fieldsetDisplayProperties = $form->addFieldset(
			'shopby_fieldset_display_properties',
			['legend' => __('Display Properties'), 'collapsable' => $this->getRequest()->has('popup')]
		);

		$displayModeField = $fieldsetDisplayProperties->addField(
			'display_mode',
			'select',
			[
				'name' => 'display_mode',
				'label' => __('Display Mode'),
				'title' => __('Display Mode'),
				'values' => $this->displayMode->toOptionArray(),
				'note' => '&nbsp;'
			]
		);

		$fieldsetDisplayProperties->addField(
			'mode_checkbox_cdp',
			'select',
			[
				'name' => 'mode_checkbox_cdp',
				'label' => __('Show Checkbox'),
				'title' => __('Show Checkbox'),
				'values' => $this->yesNo->toOptionArray(),
				'note' => '&nbsp;'
			]
		);

		if ($this->displayMode->showDefaultSwatchOptions() || $this->attributeObject->getFrontendInput() == 'price') {
			$dependence->addGroupValues(
				$displayModeField->getName(),
				self::FIELD_FRONTEND_INPUT,
				$this->displayMode->getInputTypeMap(),
				$this->displayMode->getAllOptionsDependencies()
			);
		}

		$addFromToWidget = $fieldsetDisplayProperties->addField(
			'add_from_to_widget',
			'select',
			[
				'name' => 'add_from_to_widget',
				'label' => __('Add From-To Widget'),
				'title' => __('Add From-To Widget'),
				'values' => $this->yesNo->toOptionArray()
			]
		);
		$valuesMode = [
			DisplayMode::MODE_DEFAULT,
			DisplayMode::MODE_DROPDOWN,
			DisplayMode::MODE_SLIDER
		];
		/**
		 * dependency means that all Display Modes support widget except "From-To Only" mode
		 */
		$dependence->addFieldMap(
			$addFromToWidget->getHtmlId(),
			$addFromToWidget->getName()
		)->addFieldDependence(
			$addFromToWidget->getName(),
			$displayModeField->getName(),
			$this->dependencyFieldFactory->create(
				[
					'fieldData' => [
						'separator' => ',',
						'value' => implode(',', $valuesMode),
						'negative' => false,
						'group' => 'price'
					],
					'fieldPrefix' => ''
				]
			)
		);

		$dependence->addFieldToGroup($addFromToWidget->getName(), DisplayMode::ATTRUBUTE_PRICE);

		$dependence->addFieldMap(
			$displayModeField->getHtmlId(),
			$displayModeField->getName()
		);

		$sliderMinField = $fieldsetDisplayProperties->addField(
			'slider_min',
			'text',
			[
				'name' => 'slider_min',
				'label' => __('Minimum Slider Value'),
				'title' => __('Minimum Slider Value'),
				'class' => 'validate-zero-or-greater validate-number',
				'note' => __('Please specify the min value to limit the slider, e.g. <$10')
			]
		);

		$dependence->addFieldMap(
			$sliderMinField->getHtmlId(),
			$sliderMinField->getName()
		)->addFieldDependence(
			$sliderMinField->getName(),
			$displayModeField->getName(),
			DisplayMode::MODE_SLIDER
		);

		$sliderMaxField = $fieldsetDisplayProperties->addField(
			'slider_max',
			'text',
			[
				'name' => 'slider_max',
				'label' => __('Maximum Slider Value'),
				'title' => __('Maximum Slider Value'),
				'class' => 'validate-greater-than-zero validate-number',
				'note' => __('Please specify the max value to limit the slider, e.g. >$999')
			]
		);

		$dependence->addFieldMap(
			$sliderMaxField->getHtmlId(),
			$sliderMaxField->getName()
		)->addFieldDependence(
			$sliderMaxField->getName(),
			$displayModeField->getName(),
			DisplayMode::MODE_SLIDER
		);

		$sliderStepField = $fieldsetDisplayProperties->addField(
			'slider_step',
			'text',
			[
				'name' => 'slider_step',
				'label' => __('Slider Step'),
				'title' => __('Slider Step'),
				'class' => 'validate-zero-or-greater'
			]
		);

		$dependence->addFieldMap(
			$sliderStepField->getHtmlId(),
			$sliderStepField->getName()
		)->addFieldDependence(
			$sliderStepField->getName(),
			$displayModeField->getName(),
			DisplayMode::MODE_SLIDER
		);

		////for decimal
		$valuesMode = [
			DisplayMode::MODE_DEFAULT,
			DisplayMode::MODE_DROPDOWN,
			DisplayMode::MODE_SLIDER,
			DisplayMode::MODE_FROM_TO_ONLY
		];

		if ($this->attributeObject->getAttributeCode() != DisplayMode::ATTRUBUTE_PRICE) {
			$useCurrencySymbolField = $fieldsetDisplayProperties->addField(
				'units_label_use_currency_symbol',
				'select',
				[
					'name' => 'units_label_use_currency_symbol',
					'label' => __('Measure Units'),
					'title' => __('Measure Units'),
					'values' => $this->measureUnitSource->toOptionArray(),
				]
			);
			$dependence->addFieldMap(
				$useCurrencySymbolField->getHtmlId(),
				$useCurrencySymbolField->getName()
			)->addFieldDependence(
				$useCurrencySymbolField->getName(),
				$displayModeField->getName(),
				$this->dependencyFieldFactory->create(
					[
						'fieldData' => [
							'separator' => ';',
							'value' => implode(";", $valuesMode),
							'negative' => false
						],
						'fieldPrefix' => ''
					]
				)
			);
			$dependence->addFieldToGroup($useCurrencySymbolField->getName(), DisplayMode::ATTRUBUTE_PRICE);

			$unitsLabelField = $fieldsetDisplayProperties->addField(
				'units_label',
				'text',
				[
					'name' => 'units_label',
					'label' => __('Unit Label'),
					'title' => __('Unit Label'),
				]
			);

			$dependence->addFieldMap(
				$unitsLabelField->getHtmlId(),
				$unitsLabelField->getName()
			);

			$dependence->addFieldDependence(
				$unitsLabelField->getName(),
				$displayModeField->getName(),
				$this->dependencyFieldFactory->create(
					[
						'fieldData' => [
							'separator' => ',',
							'value' => implode(",", $valuesMode),
							'negative' => false
						],
						'fieldPrefix' => ''
					]
				)
			);
			$dependence->addFieldDependence(
				$unitsLabelField->getName(),
				$useCurrencySymbolField->getName(),
				MeasureUnit::CUSTOM
			);
			$dependence->addFieldToGroup($unitsLabelField->getName(), DisplayMode::ATTRUBUTE_PRICE);
		}

		$blockPosition = $fieldsetDisplayProperties->addField(
			'block_position',
			'select',
			[
				'name' => 'block_position',
				'label' => __('Show in the Block'),
				'title' => __('Show in the Block'),
				'values' => $this->filterPlacedBlockSource->toOptionArray(),
			]
		);

		$dependence->addFieldMap(
			$blockPosition->getHtmlId(),
			$blockPosition->getName()
		);

		$fieldDisplayModeSliderDependencyNegative = $this->dependencyFieldFactory->create(
			['fieldData' => ['value' => (string)DisplayMode::MODE_SLIDER, 'negative' => true], 'fieldPrefix' => '']
		);

		$sortOptionsByField = $fieldsetDisplayProperties->addField(
			'sort_options_by',
			'select',
			[
				'name' => 'sort_options_by',
				'label' => __('Sort Options By'),
				'title' => __('Sort Options By'),
				'values' => $this->sortOptionsBy->toOptionArray(),
			]
		);

		$dependence->addFieldMap(
			$sortOptionsByField->getHtmlId(),
			$sortOptionsByField->getName()
		);

		$dependence->addFieldDependence(
			$sortOptionsByField->getName(),
			$displayModeField->getName(),
			$fieldDisplayModeSliderDependencyNegative
		);
		$dependence->addFieldToGroup($sortOptionsByField->getName(), DisplayMode::ATTRUBUTE_DEFAULT);

		$showProductQuantitiesField = $fieldsetDisplayProperties->addField(
			'show_product_quantities',
			'select',
			[
				'name' => 'show_product_quantities',
				'label' => __('Show Product Quantities'),
				'title' => __('Show Product Quantities'),
				'values' => $this->showProductQuantities->toOptionArray(),
			]
		);

		$dependence->addFieldMap(
			$showProductQuantitiesField->getHtmlId(),
			$showProductQuantitiesField->getName()
		);

		$dependence->addFieldDependence(
			$showProductQuantitiesField->getName(),
			$displayModeField->getName(),
			$this->dependencyFieldFactory->create(
				[
					'fieldData' => [
						'separator' => ';',
						'value' => implode(";", $this->displayMode->getShowProductQuantitiesConfig()),
						'negative' => false
					],
					'fieldPrefix' => ''
				]
			)
		);

		$showSearchBoxField = $fieldsetDisplayProperties->addField(
			'is_show_search_box',
			'select',
			[
				'name' => 'is_show_search_box',
				'label' => __('Show Search Box'),
				'title' => __('Show Search Box'),
				'values' => $this->yesNo->toOptionArray(),
			]
		);

		$dependence->addFieldMap(
			$showSearchBoxField->getHtmlId(),
			$showSearchBoxField->getName()
		);

		$dependence->addFieldDependence(
			$showSearchBoxField->getName(),
			$displayModeField->getName(),
			$this->dependencyFieldFactory->create(
				[
					'fieldData' => [
						'separator' => ';',
						'value' => DisplayMode::MODE_DEFAULT . ';' . DisplayMode::MODE_IMAGES_LABELS,
						'negative' => false,
					],
					'fieldPrefix' => ''
				]
			)
		);

		$showSearchBoxFieldIfManyOptions = $fieldsetDisplayProperties->addField(
			'limit_options_show_search_box',
			'text',
			[
				'name' => 'limit_options_show_search_box',
				'label' => __('Show the searchbox if the number of options more than'),
				'title' => __('Show the searchbox if the number of options more than'),
				'note' => __('Customers will be able to search for the filter option in the searchbox. Leave the field empty to hide the searchbox.')
			]
		);

		$dependence->addFieldMap(
			$showSearchBoxFieldIfManyOptions->getHtmlId(),
			$showSearchBoxFieldIfManyOptions->getName()
		);

		$dependence->addFieldDependence(
			$showSearchBoxFieldIfManyOptions->getName(),
			$showSearchBoxField->getName(),
			self::YES_NO_POSITIVE_VALUE
		);

		if (!in_array(
			$this->attributeObject->getAttributeCode(),
			[Category::ATTRIBUTE_CODE, DisplayMode::ATTRUBUTE_PRICE]
		)) {
			$showFeaturedOnlyField = $fieldsetDisplayProperties->addField(
				'show_featured_only',
				'select',
				[
					'name' => 'show_featured_only',
					'label' => __('Show Featured Only'),
					'title' => __('Show Featured Only'),
					'values' => $this->yesNo->toOptionArray(),
				]
			);
		}

		$numberUnfoldedOptionsField = $fieldsetDisplayProperties->addField(
			'number_unfolded_options',
			'text',
			[
				'name' => 'number_unfolded_options',
				'label' => __('Number of unfolded options'),
				'title' => __('Number of unfolded options'),
				'note' => __('Other options will be shown after a customer clicks the "More" button.')
			]
		);

		$dependence->addFieldMap(
			$numberUnfoldedOptionsField->getHtmlId(),
			$numberUnfoldedOptionsField->getName()
		);

		if (!in_array(
			$this->attributeObject->getAttributeCode(),
			[Category::ATTRIBUTE_CODE, DisplayMode::ATTRUBUTE_PRICE]
		)) {
			$dependence->addFieldMap(
				$showFeaturedOnlyField->getHtmlId(),
				$showFeaturedOnlyField->getName()
			);
			$dependence->addFieldDependence(
				$numberUnfoldedOptionsField->getName(),
				$showFeaturedOnlyField->getName(),
				$this->dependencyFieldFactory->create(
					[
						'fieldData' => ['value' => self::YES_NO_POSITIVE_VALUE, 'negative' => true],
						'fieldPrefix' => ''
					]
				)
			);
		}

		$dependence->addFieldDependence(
			$numberUnfoldedOptionsField->getName(),
			$displayModeField->getName(),
			$this->dependencyFieldFactory->create(
				[
					'fieldData' => [
						'separator' => ';',
						'value' => implode(";", $this->displayMode->getNumberUnfoldedOptionsConfig()),
						'negative' => false
					],
					'fieldPrefix' => ''
				]
			)
		);

		$isExpand = $fieldsetDisplayProperties->addField(
			'is_expanded',
			'select',
			[
				'name' => 'is_expanded',
				'label' => __('Expand'),
				'title' => __('Expand'),
				'values' => $this->yesNo->toOptionArray(),
			]
		);

		$dependence->addFieldMap(
			$isExpand->getHtmlId(),
			$isExpand->getName()
		);

		$dependence->addFieldDependence(
			$isExpand->getName(),
			$blockPosition->getName(),
			$this->dependencyFieldFactory->create(
				[
					'fieldData' => [
						'separator' => ';',
						'value' => FilterPlacedBlock::POSITION_SIDEBAR . ';' . FilterPlacedBlock::POSITION_BOTH,
						'negative' => false,
					],
					'fieldPrefix' => ''
				]
			)
		);

		$toolTip = $fieldsetDisplayProperties->addField(
			'tooltip',
			'text',
			[
				'name' => 'tooltip',
				'label' => __('Tooltip'),
				'title' => __('Tooltip'),
			]
		);

		$toolTip->setRenderer(
			$this->getLayout()->createBlock(\Amasty\Shopby\Block\Adminhtml\Form\Renderer\Fieldset\MultiStore::class)
		);

		$this->addCategoriesVisibleFilter($fieldsetDisplayProperties, $dependence);

		if ($this->attributeObject->getAttributeCode() == Category::ATTRIBUTE_CODE) {
			$this->addCategorySettingFields($fieldsetDisplayProperties, $dependence, $displayModeField);
		}

		$fieldsetFiltering = $form->addFieldset(
			'shopby_fieldset_filtering',
			['legend' => __('Filtering'), 'collapsable' => $this->getRequest()->has('popup')]
		);

		$dependence->addFieldsets(
			$fieldsetFiltering->getHtmlId(),
			self::FIELD_FRONTEND_INPUT,
			['value' => 'price', 'negative' => false]
		);

		$multiselectNote = $this->attributeObject->getAttributeCode() == Category::ATTRIBUTE_CODE
			? __(
				'When multiselect option is disabled it follows the '
				. 'category page (except the filtering from the search page)'
			)
			: null;

		$multiselectField = $fieldsetFiltering->addField(
			'is_multiselect',
			'select',
			[
				'name' => 'is_multiselect',
				'label' => __('Allow Multiselect'),
				'title' => __('Allow Multiselect'),
				'values' => $yesNoSource,
				'note' => $multiselectNote,
			]
		);
		$dependence->addFieldMap(
			$multiselectField->getHtmlId(),
			$multiselectField->getName()
		);
		$dependence->addFieldDependence(
			$multiselectField->getName(),
			$displayModeField->getName(),
			$this->dependencyFieldFactory->create(
				[
					'fieldData' => [
						'separator' => ';',
						'value' => implode(";", $this->displayMode->getIsMultiselectConfig()),
						'negative' => false
					],
					'fieldPrefix' => ''
				]
			)
		);

		if ($this->attributeObject->getAttributeCode() != Category::ATTRIBUTE_CODE) {
			$useAndLogicField = $fieldsetFiltering->addField(
				'is_use_and_logic',
				'select',
				[
					'name' => 'is_use_and_logic',
					'label' => __('Multiple Values Logic'),
					'title' => __('Multiple Values Logic'),
					'values' => $this->multipleValuesLogic->toOptionArray(),
				]
			);

			$dependence->addFieldMap(
				$useAndLogicField->getHtmlId(),
				$useAndLogicField->getName()
			)->addFieldDependence(
				$useAndLogicField->getName(),
				$multiselectField->getName(),
				$this->dependencyFieldFactory->create(
					[
						'fieldData' => [
							'separator' => ';',
							'value' => implode(";", $this->displayMode->getIsMultiselectConfig()),
							'negative' => false
						],
						'fieldPrefix' => ''
					]
				)
			);
		}

		if ($this->attributeObject->getAttributeCode() != Category::ATTRIBUTE_CODE
			&& $this->attributeObject->getFrontendInput() != 'price' ) {
			$fieldsetDisplayProperties->addField(
				'show_icons_on_product',
				'select',
				[
					'name' => 'show_icons_on_product',
					'label' => __('Show Icon on the Product Page'),
					'title' => __('Show Icon on the Product Page'),
					'note' => __('Upload images for your options to show them right after the product title'),
					'values' => $yesNoSource,
				]
			);
		}

		$this->setChild(
			'form_after',
			$dependence
		);

		$this->_eventManager->dispatch(
			'amshopby_attribute_form_tab_build_after',
			['form' => $form, 'setting' => $this->setting, 'dependence' => $dependence]
		);

		$this->setForm($form);
		$data = $this->setting->getData();

		if (isset($data['slider_step'])) {
			$data['slider_step'] = round($data['slider_step'], 4);
		}

		$form->setValues($data);
		return \Magento\Backend\Block\Widget\Form::_prepareForm();
	}

}
