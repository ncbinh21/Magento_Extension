<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */
namespace Forix\Bannerslider\Block\Adminhtml\Banner\Edit\Tab;

use Forix\Bannerslider\Model\Status;

/**
 * Banner Edit tab.
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Banner extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_objectFactory;

    /**
     * value collection factory.
     *
     * @var \Forix\Bannerslider\Model\ResourceModel\Value\CollectionFactory
     */
    protected $_valueCollectionFactory;

    /**
     * slider factory.
     *
     * @var \Forix\Bannerslider\Model\SliderFactory
     */
    protected $_sliderFactory;

    /**
     * @var \Forix\Bannerslider\Model\Banner
     */
    protected $_banner;

	protected $_wysiwygConfig;

    /**
     * constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                        $context
     * @param \Magento\Framework\Registry                                    $registry
     * @param \Magento\Framework\Data\FormFactory                            $formFactory
     * @param \Magento\Framework\DataObjectFactory                               $objectFactory
     * @param \Forix\Bannerslider\Model\Banner                           $banner
     * @param \Forix\Bannerslider\Model\ResourceModel\Value\CollectionFactory $valueCollectionFactory
     * @param \Forix\Bannerslider\Model\SliderFactory                    $sliderFactory
     * @param array                                                          $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Forix\Bannerslider\Model\Banner $banner,
        \Forix\Bannerslider\Model\ResourceModel\Value\CollectionFactory $valueCollectionFactory,
        \Forix\Bannerslider\Model\SliderFactory $sliderFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_objectFactory = $objectFactory;
        $this->_banner = $banner;
        $this->_valueCollectionFactory = $valueCollectionFactory;
        $this->_sliderFactory = $sliderFactory;
		$this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * prepare layout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());

        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Forix\Bannerslider\Block\Adminhtml\Form\Renderer\Fieldset\Element',
                $this->getNameInLayout().'_fieldset_element'
            )
        );

        return $this;
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $advancedAttributes = $this->_banner->getStoreAttributes();
        $advancedAttributesInStores = ['store_id' => ''];

        foreach ($advancedAttributes as $advancedAttribute) {
            $advancedAttributesInStores[$advancedAttribute.'_in_store'] = '';
        }

        $dataObj = $this->_objectFactory->create(
            ['data' => $advancedAttributesInStores]
        );
        $model = $this->_coreRegistry->registry('banner');

        if ($sliderId = $this->getRequest()->getParam('current_slider_id')) {
            $model->setSliderId($sliderId);
        }

        $dataObj->addData($model->getData());

        $storeViewId = $this->getRequest()->getParam('store');

        $attributesInStore = $this->_valueCollectionFactory
            ->create()
            ->addFieldToFilter('banner_id', $model->getId())
            ->addFieldToFilter('store_id', $storeViewId)
            ->getColumnValues('attribute_code');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix($this->_banner->getFormFieldHtmlIdPrefix());

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Image Information')]);

        if ($model->getId()) {
            $fieldset->addField('banner_id', 'hidden', ['name' => 'banner_id']);
        }

        $elements = [];

        $elements['name'] = $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
				'note'	   => 'Name to recognize banner in admin'
            ]
        );

		$elements['title_banner'] = $fieldset->addField(
			'title_banner',
			'editor',
			[
				'name' => 'title_banner',
				'label' => __('Title'),
				'title' => __('Title'),
				'rows' => '5',
				'cols' => '30',
				'wysiwyg' => true,
				'config' => $this->_wysiwygConfig->getConfig([
					'add_variables' => false,
					'add_widgets' => false,
					'add_images' => false,
					'insert_images'=>false
				]),
				'note' => 'Show title on banner'
			]
		);

        $elements['status'] = $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'options' => Status::getAvailableStatuses(),
            ]
        );

        $slider = $this->_sliderFactory->create()->load($sliderId);

        if ($slider->getId()) {
            $elements['slider_id'] = $fieldset->addField(
                'slider_id',
                'select',
                [
                    'label' => __('Slider'),
                    'name' => 'slider_id',
                    'values' => [
                        [
                            'value' => $slider->getId(),
                            'label' => $slider->getTitle(),
                        ],
                    ],
                ]
            );
        } else {
            $elements['slider_id'] = $fieldset->addField(
                'slider_id',
                'select',
                [
                    'label' => __('Slider'),
                    'name' => 'slider_id',
                    'values' => $model->getAvailableSlides(),
                ]
            );
        }

        $elements['image_alt'] = $fieldset->addField(
            'image_alt',
            'editor',
            [
                'title' => __('Short Description'),
                'label' => __('Short Description'),
                'name' => 'image_alt',
				'rows' => '5',
				'cols' => '30',
				'wysiwyg' => true,
				'config' => $this->_wysiwygConfig->getConfig()
            ]
        );

        $elements['click_url'] = $fieldset->addField(
            'click_url',
            'text',
            [
                'title' => __('URL'),
                'label' => __('URL'),
                'name' => 'click_url',
            ]
        );

        $elements['button_text'] = $fieldset->addField(
            'button_text',
            'text',
            [
                'title' => __('Button (Link) Text'),
                'label' => __('Button (Link) Text'),
                'name' => 'button_text'
            ]
        );
        $elements['target'] = $fieldset->addField(
            'target',
            'select',
            [
                'label' => __('Target'),
                'name' => 'target',
                'values' => [
                    [
                        'value' => \Forix\Bannerslider\Model\Banner::BANNER_TARGET_BLANK,
                        'label' => __('Open in a new tab'),
                    ],
                    [
                        'value' => \Forix\Bannerslider\Model\Banner::BANNER_TARGET_SELF,
                        'label' => __('Open in the same frame as it was clicked'),
                    ]
                    /*[
                        'value' => \Forix\Bannerslider\Model\Banner::BANNER_TARGET_PARENT,
                        'label' => __('Parent Window with Browser Navigation'),
                    ],*/

                ],
            ]
        );

        $elements['align_text'] = $fieldset->addField(
            'align_text',
            'select',
            [
                'label' => __('Text Placement'),
                'title' => __('Text Placement'),
                'name' => 'align_text',
                'options' => Status::getTextAlign(),
            ]
        );
        
        $imageNames = ['image' => 'Desktop','tablet' => 'Tablet','phone' => 'Mobile', 'right_image' => 'Right Image'];
        foreach($imageNames as $key => $name){
                $elements[$key] = $fieldset->addField(
                    $key,
                    'image',
                    [
                        'title' => __($name),
                        'label' => __($name),
                        'name' => $key,
                        'note' => 'Allowed file types: jpg, jpeg, gif, png',

                    ]
                );
            /*$elements[$key.'-thumb'] = $fieldset->addField(
                $key.'_thumb',
                'image',
                [
                    'title' => __($name . ' Thumbnail'),
                    'label' => __($name . ' Thumbnail'),
                    'name' => $key.'_thumb',
                    'note' => 'Allowed file types: jpg, jpeg, gif, png',
                ]
            );*/
        }

        /*$dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $timeFormat = $this->_localeDate->getTimeFormat(\IntlDateFormatter::SHORT);

        if($dataObj->hasData('start_time')) {
            $datetime = new \DateTime($dataObj->getData('start_time'));
            $dataObj->setData('start_time', $datetime->setTimezone(new \DateTimeZone($this->_localeDate->getConfigTimezone())));
        }

        if($dataObj->hasData('end_time')) {
            $datetime = new \DateTime($dataObj->getData('end_time'));
            $dataObj->setData('end_time', $datetime->setTimezone(new \DateTimeZone($this->_localeDate->getConfigTimezone())));
        }

        $style = 'color: #000;background-color: #fff; font-weight: bold; font-size: 13px;';
        $elements['start_time'] = $fieldset->addField(
            'start_time',
            'date',
            [
                'name' => 'start_time',
                'label' => __('Starting time'),
                'title' => __('Starting time'),
                'required' => false,
                'readonly' => true,
                'style' => $style,
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'note' => $this->_localeDate->getDateTimeFormat(\IntlDateFormatter::SHORT),
            ]
        );

        $elements['end_time'] = $fieldset->addField(
            'end_time',
            'date',
            [
                'name' => 'end_time',
                'label' => __('Ending time'),
                'title' => __('Ending time'),
                'required' => false,
                'readonly' => true,
                'style' => $style,
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'note' => $this->_localeDate->getDateTimeFormat(\IntlDateFormatter::SHORT)
            ]
        );*/



        foreach ($attributesInStore as $attribute) {
            if (isset($elements[$attribute])) {
                $elements[$attribute]->setStoreViewId($storeViewId);
            }
        }
        $form->addValues($dataObj->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getBanner()
    {
        return $this->_coreRegistry->registry('banner');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getBanner()->getId()
            ? __("Edit Image '%1'", $this->escapeHtml($this->getBanner()->getName())) : __('New Image');
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Image Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Image Information');
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
}
