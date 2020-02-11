<?php

namespace Orange35\ImageConstructor\Block;

use \Magento\Catalog\Block\Product\View\AbstractView;
use \Magento\Catalog\Block\Product\Context;
use \Magento\Framework\Stdlib\ArrayUtils;
use \Magento\Framework\Json\EncoderInterface;
use Orange35\ImageConstructor\Helper\Image as HelperImage;
use \Magento\Catalog\Helper\Image as CatalogImage;

class Constructor extends AbstractView
{
    const XML_PATH_PRODUCT = 'image_constructor_section/product_page/';
    /**
     * @var Uploader
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    protected $_scopeConfig;
    protected $_storeScope;

    /**
     * @var CatalogImage
     */
    protected $catalogHelper;

    /**
     * Constructor constructor.
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param EncoderInterface $jsonEncoder
     * @param HelperImage $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        EncoderInterface $jsonEncoder,
        HelperImage $helper,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
        $this->jsonEncoder = $jsonEncoder;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $this->helper = $helper;
    }

    protected function getFromSystem($name)
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_PRODUCT . $name, $this->_storeScope);
    }

    protected function getImageBySystem($value, $size)
    {
        return $this->helper->getImageUrl(
            $value,
            $this->_scopeConfig->getValue(self::XML_PATH_PRODUCT . $size . '_image_width', $this->_storeScope),
            $this->_scopeConfig->getValue(self::XML_PATH_PRODUCT . $size . '_image_height', $this->_storeScope),
            $this->_scopeConfig->getValue(self::XML_PATH_PRODUCT . $size . '_keep_frame', $this->_storeScope)
        );

    }

    public function getOptionsJson()
    {
        $options = [
            'productFormSelector' => $this->getFromSystem('product_form_selector'), // '#product_addtocart_form'
            'mediaGalleryPlaceholderSelector' => $this->getFromSystem('media_gallery_placeholder_selector'), // '[data-gallery-role=gallery-placeholder]'
            'mediaGallerySelector' => $this->getFromSystem('media_gallery_selector'), // '[data-gallery-role="gallery"]'
            'imageHolderSelector' => $this->getFromSystem('image_holder_selector'), // '.fotorama__stage__frame:first'
            'imageSelector' => $this->getFromSystem('image_selector'), // '.fotorama__img'
            'imageZoomSelector' => $this->getFromSystem('image_zoom_selector') // '.fotorama__img--full'
        ];
        foreach ($this->getOptions() as $option) {
            if (is_array($option->getValues())) {
                foreach ($option->getValues() as $value) {
                    if ($value->getLayer()) {
                        $options['layers'][] = [
                            'optionId' => $value->getOptionTypeId(),
                            'smallImage' => $this->getImageBySystem($value->getLayer(), 'small'),
                            'mediumImage' => $this->getImageBySystem($value->getLayer(), 'medium'),
                            'largeImage' => $this->getImageBySystem($value->getLayer(), 'large'),
                            'sortOrder' => $value->getSortOrder(),
                            'sortOrderOption' => $option->getSortOrder(),
                        ];
                    }
                }
            }
        }
        return $this->jsonEncoder->encode($options);
    }

    public function getOptions()
    {
        $options = $this->getProduct()->getOptions();
        return $options;
    }

}