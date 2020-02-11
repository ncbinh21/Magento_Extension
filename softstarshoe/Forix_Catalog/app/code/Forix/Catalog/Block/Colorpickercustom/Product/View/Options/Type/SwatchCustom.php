<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Product options text type block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Forix\Catalog\Block\Colorpickercustom\Product\View\Options\Type;

use Magento\Catalog\Model\Product\Option;

class SwatchCustom extends \Orange35\Colorpickercustom\Block\Product\View\Options\Type\Swatch
{
    public function getValuesHtml()
    {
        $_option = $this->getOption();
        $id = $_option->getId();

        //Previously selected values (cart item)
        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $_option->getId());

        //If option is required
        $require = $_option->getIsRequire();

        //If option is allowed to have multiple values selected
        $multiple = $_option->getType() == Option::OPTION_TYPE_MULTIPLE || $_option->getType() == Option::OPTION_TYPE_CHECKBOX;
        $sliderSwatches = $this->_configHelper->getConfig('slider/slider_swatches');
        $sliderActive = $_option->getValuesCollection()->count() > $sliderSwatches;
        $sliderType = $sliderActive ? $this->_configHelper->getConfig('slider/slider_type') : 0;

        //Option swatches sizes
        $width = $_option->getData('swatch_width');
        $height = $_option->getData('swatch_height');

        //Swatch tooltip sizes
        $tooltipWidth = $this->_configHelper->getConfig('tooltip/tooltip_width');
        $tooltipHeight = $this->_configHelper->getConfig('tooltip/tooltip_height');
        $tooltipPadding = $this->_configHelper->getConfig('tooltip/tooltip_padding');

        $size = 'width: ' . $width . 'px; height: ' . $height . 'px;';

        //Strings for option controls rendering
        $valueSelected = '';
        $selectedTitle = '';
        $selectedValue = '';

        //Defining value-selected attribute for previously selected options
        if ($configValue) {
            if ($multiple) {
                $valueSelected = ' value-selected="';
            } else {
                $valueSelected = ' value-selected="' . $configValue;
                $selectedValue = $configValue;
            }
        }

        //String to append swatches
        $swatches = '';
        $flagSelect = '';
        foreach (array_values($_option->getValues()) as $_key => $_value) {
            $color = $_value->getColor();
            $image = $_value->getImage();
            $valueId = $_value->getId();

            //Defining type of swatch, 1: Color, 2: Image, 3: Clear
            $type = $image ? 2 : ($color ? 1 : 3);

            //Setting value of swatch, image > color
            $value = ($type == '2') ? $this->_imageHelper->getImageUrl($image, $width, $height) : $color;
            $thumb = $image ? $this->_imageHelper->getImageUrl($image, $tooltipWidth, $tooltipHeight) : '';

            //Selected class string
            $selected = '';

            //Getting price of value to append it to a title
            $price = $this->pricingHelper->currency($_value->getPrice(true), true, false);

            $title = $_value->getTitle() . ' +' . $price;
            if($_value->getPrice() == 0) {
                $title = $_value->getTitle();
            }
            //Setting selected class, appending title and value for previously selected values
            if ($multiple && is_array($configValue) && in_array($valueId, $configValue)) {
                $selected = ' selected';
                $valueSelected .= $valueId . ' ';
                if ($selectedTitle == '')
                    $selectedTitle .= $title;
                else
                    $selectedTitle .= ', ' . $title;
            } elseif ($valueId == $configValue) {
                $mediaImg = $this->getUrl('pub/media') . 'catalog/o35/colorpicker' . $_value->getImage();
                $selected = ' selected';
                $selectedTitle = $title . '<img src="' . $mediaImg .'"/>';
                $flagSelect = 'selected';
            }

            //Option controls
            $attr =
                ' value-type="' . $type . '"' .
                ' value-id="' . $valueId . '"' .
                ' value-label="' . $title . '"' .
                ' value-tooltip-thumb="' . $thumb . '"' .
                ' value-tooltip-type="' . $_option->getData('tooltip') . '"' .
                ' value-tooltip-width="' . $tooltipWidth . '"' .
                ' value-tooltip-height="' . $tooltipHeight . '"' .
                ' value-tooltip-padding="' . $tooltipPadding . '"' .
                ' value-tooltip-value="' . $value . '"';

            //Opening item div
            if ($sliderType == 1)
                $swatches .= '<div class="item">';
            elseif ($sliderType == 2) {
                if ($_key == 0)
                    $swatches .= '<div class="item">';
                elseif (!($_key % $sliderSwatches))
                    $swatches .= '</div><div class="item">';
            }

            //Appending swatch corresponding to its type, 1: Color, 2: Image, 3: Clear
            switch ($type) {
                case 1:
                    $swatches .= '<div class="' . static::VALUE_CLASS . ' color' . $selected . '" ' . $attr .
                        '" style="background: ' . $value .
                        ' no-repeat center; background-size: initial; ' . $size . '"></div>';
                    break;
                case 2:
                    $swatches .= '<div class="' . static::VALUE_CLASS . ' image' . $selected . '" ' . $attr .
                        '" style="background: url(' . $value . ') no-repeat center; background-size: initial; ' . $size . '"></div>';
                    break;
                case 3:
                    $swatches .= '<div class="' . static::VALUE_CLASS . $selected . '" ' . $attr . 'style="' . $size . '"></div>';
                    break;
                default:
                    $swatches .= '<div class="' . static::VALUE_CLASS . $selected . '" ' . $attr . 'style="' . $size . '">' . $title . '</div>';
                    break;
            }

            //Closing item div
            if ($sliderType == 1)
                $swatches .= '</div>';
        }

        if ($sliderType == 2)
            $swatches .= '</div>';

        //Closes " if needed
        if ($valueSelected != '')
            $valueSelected .= '"';

        $requiredLabel = $require ? ' data-required="1"' : '';

        //Creating multiple attribute to option controls if multiple values can be selected
        $multipleAttribute = $multiple ? 'multiple="true"' : '';

        $active = $activeClass = '';
        if($this->getOption()->getPosition() == '1'){
            $active  = 'active';
        }
        if($active) {
            $activeClass = 'active';
        }
        //Creating label for option
        $label = '<div class="swatch-option-title-align"><span class="' . static::OPTION_LABEL_CLASS . ' ' . $active .'"' . $requiredLabel . '>' . $this->getOption()->getPosition() . '. '  .  $_option->getTitle() . '</span>'
            . '<span class="' . static::OPTION_SELECTED_VALUE_LABEL_CLASS . '">' . $selectedTitle . '</span></div>';

        $preSlider = '';
        $postSlider = '';

        //Creating carousel divs and controls
        if ($sliderType) {
            $preSlider = '<div id="carousel-option-' . $id . '" class="carousel slide carousel-swatches" data-interval="false" data-ride="carousel">
                <div class="carousel-inner" role="listbox">';
            $postSlider = '</div></div>';
        }

        //Putting all things together
        $result = '<div class="o35-swatch-opt-item ' . $activeClass . '"><div class="field' . ($require ? ' required' : '') . ' ' . static::OPTION_CLASS . ' ' . $id . ' ' . $flagSelect
            . '" option-code="' . $id
            . '" option-id="' . $id . '"'
            . $multipleAttribute
            . $valueSelected . '>'
            . $label
            . '<div class="' . static::OPTION_VALUES_WRAPPER . ' clearfix ' . $active .'">'
            . $preSlider
            . $swatches
            . $postSlider
            . '</div>'
            // styles required to hide element but show error message when element is required
            . str_replace('<select ', '<select style="border:0; height: 0; padding: 0; margin: 0; visibility: hidden;" ', \Magento\Catalog\Block\Product\View\Options\Type\Select::getValuesHtml())
            . '</div></div>';

        return $result;
    }
}
