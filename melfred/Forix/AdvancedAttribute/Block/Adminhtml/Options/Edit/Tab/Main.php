<?php
namespace Forix\AdvancedAttribute\Block\Adminhtml\Options\Edit\Tab;

use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Forix\AdvancedAttribute\Model\Config\Source\OptionManufacturer;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    const IS_ACTIVE_YES = 1;
    const IS_ACTIVE_NO = 2;

    protected $objectManager;
    protected $_request;
    protected $_optionModel;
    protected $_existBannerOptions = array();
    protected $_optionHelper;
    protected $_optionManufacturer;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        WysiwygConfig $wysiwygConfig,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Eav\Api\AttributeRepositoryInterface $eavAttributeRepository,
        \Magento\Framework\App\Request\Http $request,
        \Forix\AdvancedAttribute\Model\OptionFactory $optionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Forix\AdvancedAttribute\Helper\Option $optionHelper,
        OptionManufacturer $optionManufacturer,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->objectManager = $eavAttributeRepository;
        $this->_request = $request;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->_optionModel = $optionFactory;
        $this->_registry = $registry;
        $this->_optionHelper = $optionHelper;
		$this->_optionManufacturer = $optionManufacturer;
    }

    /**
     * Add fields to base fieldset which are general to sales reports
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $htmlIdPrefix = 'rule_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $form->setFieldNameSuffix('options');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Option Form')]);
        $_attributeCode = $this->_request->getParam('attrcode');
        $_attributeId = $this->_request->getParam('attrid');

        $fieldset->addField('attribute_code', 'hidden', ['name' => 'attribute_code', 'value' => $_attributeCode]);
        $fieldset->addField('attribute_id', 'hidden', ['name' => 'attribute_id', 'value' => $_attributeId]);
        $fieldset->addType('image', 'Forix\AdvancedAttribute\Block\Adminhtml\Options\Helper\Image');

        $fieldset->addField(
            'option_id',
            'select',
            [
                'name' => 'option_id',
                'options' => $this->getAllOptionAttribute($_attributeCode),
                'label' => __('Select Option'),
                'required' => true
            ]
        );

		$fieldset->addField(
			'name',
			'text',
			[
				'name' => 'name',
				'label' => __('Name Display'),
				'title' => __('Name Display'),
				'note' => 'Name Display'
			]
		);

        $fieldset->addField(
            'icon_normal',
            'image',
            [
                'name' => 'icon_normal',
                'label' => __('Icon'),
                'title' => __('Icon'),
                'note' => 'Icon For Ground Condition'
            ]
        );

//        $fieldset->addField(
//            'icon_page',
//            'image',
//            [
//                'name' => 'icon_page',
//                'label' => __('Icon Image (Page)'),
//                'title' => __('Icon Image (Page)'),
//            ]
//        );

        $fieldset->addField(
            'icon_hover',
            'image',
            [
                'name' => 'icon_hover',
                'label' => __('Banner Image (Page)'),
                'title' => __('Banner Image (Page)'),
            ]
        );


        $fieldset->addField(
            'url_key',
            'text',
            [
                'name' => 'url_key',
                'label' => __('Url Key'),
                'title' => __('Url Key'),
                'note' => 'Not Duplicate. Maximum 250 chars'
            ]
        );
        
        $fieldset->addField(
            'content',
            'editor',
            [
                'name' => 'content',
                'label' => __('Content'),
                'title' => __('Content'),
                'config' => $this->wysiwygConfig->getConfig()
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'options' => array(self::IS_ACTIVE_YES => 'Yes', self::IS_ACTIVE_NO => 'No'),
                'label' => __('Active'),
                'title' => __('Active')
            ]
        );

        // Field for RigModel
		$attrCode =  $this->_request->getParam('attrcode');
		if ($attrCode == 'mb_rig_model') {

			$fieldset = $form->addFieldset('base_fieldset_rig', ['legend' => __('Rig Model Form')]);
			$optionsManufacture = $this->_optionManufacturer->toOptionArray();

			$fieldset->addField('mb_oem',
				'select',
				[
					'name' => 'mb_oem',
					'values' => $optionsManufacture,
					'label' => 'Choose Manufacture',
					'title' => 'Choose Manufacture'
				]
			);
		}

		if ($attrCode == "mb_oem") {
			$fieldset = $form->addFieldset('base_fieldset_rig', ['legend' => __('For Manufacturer')]);
			$fieldset->addField(
				'mb_manufacturer_color',
				'text',
				[
					'name'  => 'mb_manufacturer_color',
					'label' => __('Color border of bottom'),
					'title' => __('Color border of bottom'),
					'note'  => 'EX: #FEC53B'
				]
			);
		}


		$bannerId = (int)$this->getRequest()->getParam('id');
		if ($bannerId > 0) {
			$fieldset->addField('banner_id', 'hidden', ['name' => 'banner_id', 'value' => $bannerId]);
			$optionBanner = $this->_registry->registry('current_banner_option');
			$form->addValues($optionBanner->getData());
		} else {
			$optionBanner = $this->_optionModel->create();
			$form->addValues($optionBanner->getDefaultValues());
		}

        //$form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get option for attribute
     *
     * @param string attribute code
     * @return array
     */
    protected function getAllOptionAttribute($attributeCode)
    {
        // Access to the attribute interface
        $attribute = $this->objectManager->get(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE, $attributeCode);

        $result = array();
        // Get an array of options
        $options = $attribute->getOptions();
        $bannerOptionId = $this->_request->getParam('id');
        if (!empty($bannerOptionId)) {
            $currentOption = $this->_registry->registry('current_banner_option');
            if ($currentOption->getOptionId()) {
                $optionId = $currentOption->getOptionId();
            }
        }
        if (!empty($optionId)) {
            foreach ($options as $option) {
                if ($optionId == $option->getValue()) {
                    $result[$option->getValue()] = $option->getLabel();
                    break;
                }
            }
        } else {
            $this->_existBannerOptions = $this->_optionHelper->getExistBannerOptionsByAttributeId($this->_request->getParam('attrid'));
            foreach ($options as $option) {
                if (in_array($option->getValue(), $this->_existBannerOptions)) {
                    continue;
                }
                $result[$option->getValue()] = $option->getLabel();

            }
        }

        return $result;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Main');
    }
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Main');
    }
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

}