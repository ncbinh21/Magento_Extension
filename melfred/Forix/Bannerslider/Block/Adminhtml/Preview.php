<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */
namespace Forix\Bannerslider\Block\Adminhtml;

use Forix\Bannerslider\Model\Slider as SliderModel;

/**
 * Preview Block
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Preview extends \Magento\Backend\Block\Template
{
    const STYLESLIDE_PARAM = 'sliderpreview_id';
    const PREVIEW_NOTE_ID_MIN = 1;
    const PREVIEW_NOTE_ID_MAX = 8;

    /**
     * preview template for slider.
     */
    const STYLESLIDE_EVOLUTION_PREVIEW_TEMPLATE = 'Forix_Bannerslider::slider/preview/evolution.phtml';
    const STYLESLIDE_SPECIAL_NOTE_PREVIEW_TEMPLATE = 'Forix_Bannerslider::slider/preview/special/note.phtml';
    const STYLESLIDE_FLEXSLIDER_PREVIEW_TEMPLATE = 'Forix_Bannerslider::slider/preview/slider.phtml';
    const STYLESLIDE_CUSTOM_PREVIEW_TEMPLATE = 'Forix_Bannerslider::slider/preview/custom.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Add elements in layout.
     *
     * @return
     */
    protected function _prepareLayout()
    {
        $styleslideParam = $this->getRequest()->getParam(self::STYLESLIDE_PARAM);
        $this->setStyleSlidePreviewTemplate($styleslideParam);

        return parent::_prepareLayout();
    }

    /**
     * set style slide template.
     *
     * @return string|null
     */
    public function setStyleSlidePreviewTemplate($styleslideParam)
    {
        switch ($styleslideParam) {
            case SliderModel::STYLESLIDE_EVOLUTION_ONE:
            case SliderModel::STYLESLIDE_EVOLUTION_TWO:
            case SliderModel::STYLESLIDE_EVOLUTION_THREE:
            case SliderModel::STYLESLIDE_EVOLUTION_FOUR:
                $this->setTemplate(self::STYLESLIDE_EVOLUTION_PREVIEW_TEMPLATE);
                break;
            case SliderModel::STYLESLIDE_SPECIAL_NOTE:
                $this->setTemplate(self::STYLESLIDE_SPECIAL_NOTE_PREVIEW_TEMPLATE);
                break;
            case SliderModel::STYLESLIDE_FLEXSLIDER_ONE:
            case SliderModel::STYLESLIDE_FLEXSLIDER_TWO:
            case SliderModel::STYLESLIDE_FLEXSLIDER_THREE:
            case SliderModel::STYLESLIDE_FLEXSLIDER_FOUR:
                $this->setTemplate(self::STYLESLIDE_FLEXSLIDER_PREVIEW_TEMPLATE);
                break;
            case SliderModel::STYLESLIDE_CUSTOM:
            case SliderModel::STYLESLIDE_BANNER:
                $this->setTemplate(self::STYLESLIDE_CUSTOM_PREVIEW_TEMPLATE);
                break;
        }
    }

    /**
     * get slider evolution transition array.
     *
     * @return array
     */
    public function getSliderEvolutionTransitionArray()
    {
        return [
            SliderModel::STYLESLIDE_EVOLUTION_ONE   => 'explode',
            SliderModel::STYLESLIDE_EVOLUTION_TWO   => ['barLeft', 'barRight'],
            SliderModel::STYLESLIDE_EVOLUTION_THREE => 'square',
            SliderModel::STYLESLIDE_EVOLUTION_FOUR  => 'squareRandom',
        ];
    }
}
