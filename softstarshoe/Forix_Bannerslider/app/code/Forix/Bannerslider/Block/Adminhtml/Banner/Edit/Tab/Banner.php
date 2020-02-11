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
        array $data = []
    ) {
        $this->_objectFactory = $objectFactory;
        $this->_banner = $banner;
        $this->_valueCollectionFactory = $valueCollectionFactory;
        $this->_sliderFactory = $sliderFactory;
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
        $bannerAttributes = $this->_banner->getStoreAttributes();
        $bannerAttributesInStores = ['store_id' => ''];

        foreach ($bannerAttributes as $bannerAttribute) {
            $bannerAttributesInStores[$bannerAttribute.'_in_store'] = '';
        }

        $dataObj = $this->_objectFactory->create(
            ['data' => $bannerAttributesInStores]
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
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
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
            'textarea',
            [
                'title' => __('Short Description'),
                'label' => __('Short Description'),
                'name' => 'image_alt'
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
        $elements['button_html'] = $fieldset->addField(
            'button_html',
            'editor',
            [
                'name' => 'button_html',
                'label' => __('Button Html'),
                'title' => __('Button Html'),
                'style'    => 'height:10em',
                'wysiwyg' => true,
                'required' => false,
            ]
        );
        
        $imageNames = ['image' => 'Desktop','tablet' => 'Tablet','phone' => 'Mobile'];
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
            $elements[$key.'-thumb'] = $fieldset->addField(
                $key.'_thumb',
                'image',
                [
                    'title' => __($name . ' Thumbnail'),
                    'label' => __($name . ' Thumbnail'),
                    'name' => $key.'_thumb',
                    'note' => 'Allowed file types: jpg, jpeg, gif, png',
                ]
            );
        }
        
        $elements['target'] = $fieldset->addField(
            'target',
            'select',
            [
                'label' => __('Target'),
                'name' => 'target',
                'values' => [
                    [
                        'value' => \Forix\Bannerslider\Model\Banner::BANNER_TARGET_SELF,
                        'label' => __('New Window with Browser Navigation'),
                    ],
                    [
                        'value' => \Forix\Bannerslider\Model\Banner::BANNER_TARGET_PARENT,
                        'label' => __('Parent Window with Browser Navigation'),
                    ],
                    [
                        'value' => \Forix\Bannerslider\Model\Banner::BANNER_TARGET_BLANK,
                        'label' => __('New Window without Browser Navigation'),
                    ],
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
