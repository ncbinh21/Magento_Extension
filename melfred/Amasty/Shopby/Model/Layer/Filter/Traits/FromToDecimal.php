<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Model\Layer\Filter\Traits;

use \Amasty\Shopby\Model\Source\DisplayMode;

trait FromToDecimal
{
    use FilterTrait;

    /**
     * set from and to values for decimal filter
     * @param $from
     * @param $to
     *
     * @return $this
     */
    protected function setFromTo($from, $to)
    {
        list($from, $to) = $this->prepareFromTo($from, $to);
        $this->setCurrentValue(['from'=>$from, 'to'=>$to]);
        return $this;
    }

    /**
     * @return null
     */
    public function getCurrentFrom()
    {
        return $this->getCurrentByKey('from');
    }

    /**
     * @return null
     */
    public function getCurrentTo()
    {
        return $this->getCurrentByKey('to');
    }

    /**
     * @param $key
     *
     * @return null
     */
    protected function getCurrentByKey($key)
    {
        $current = null;
        if ($this->hasCurrentValue()) {
            $current = $this->currentValue[$key];
        }
        return $current;
    }

    /**
     * @param $from
     * @param $to
     *
     * @return array
     */
    protected function prepareFromTo($from, $to)
    {
        if ($from > $to) {
            $toTmp = $to;
            $to = $from;
            $from = $toTmp;
        }

        return [$from, $to];
    }

    public function getSignsCount($number, $slider = 1)
    {
        if (($number > 0 && $number < 1) && $slider) {
            $number = $this->trimZeros((string)$number);
            $pos = strpos($number, ".");
            if ($pos !== false) {
                return strlen($number) - $pos;
            }
        }

        return 0;
    }

    public function getFloatNumber($size)
    {
        if (!$size) {
            $size = 3;
        }

        return (float) 1 / (int)str_pad('1', $size, '0', STR_PAD_RIGHT);
    }

    protected function trimZeros($str)
    {
        preg_match("/(\d\.\d*?[1-9]+)/i", $str, $matches);
        return $matches[0];
    }

    /**
     * @param $min
     * @param $sliderMin
     * @return mixed
     */
    protected function getMin($min, $sliderMin)
    {
        if ($sliderMin) {
            $min = ($sliderMin < $min) ? $min : $sliderMin;
        }

        return $min;
    }

    /**
     * @param $max
     * @param $sliderMax
     * @return mixed
     */
    protected function getMax($max, $sliderMax)
    {
        if ($sliderMax) {
            $max = ($sliderMax > $max) ? $max : $sliderMax;
        }

        return $max;
    }

    /**
     * @param $from
     * @param $min
     * @return bool
     */
    private function isIdentically($from, $min)
    {
        return (floor($from) == floor($min));
    }

    private function getExtremeValues(\Amasty\ShopbyBase\Model\FilterSetting $filterSetting, $facets, $currencyRate = 0)
    {
        $from = $this->getCurrentFrom();
        $to = $this->getCurrentTo();

        return ['from' => $from, 'to' => $to];
    }

    private function getConfig($attrType)
    {
        $config = [
            'from' => null,
            'to' => null,
            'min' => null,
            'max' => null,
            'requestVar' => null,
            'step' => null,
            'template' => null,
            'curRate' => 1,
        ];

        $filterSetting = $this->settingHelper->getSettingByLayerFilter($this);

        if ((string)$filterSetting->getDisplayMode() === (string)DisplayMode::MODE_SLIDER ||
            (string)$filterSetting->getDisplayMode() === (string)DisplayMode::MODE_FROM_TO_ONLY ||
            (bool)$filterSetting->getAddFromToWidget()
        ) {
            $facets = $this->getFacetedData();

            if (!isset($facets['data'])) {
                return $config;
            }

            $min = $this->getMin(floatval($facets['data']['min']), $filterSetting->getSliderMin());
            $max = $this->getMax(floatval($facets['data']['max']), $filterSetting->getSliderMax());

            if ($attrType == DisplayMode::ATTRUBUTE_PRICE) {
                $min *= $this->getCurrencyRate();
                $max *= $this->getCurrencyRate();
            }

            if ($min == $max) {
                return $config;
            }

            $from = !empty($this->getCurrentFrom()) ? floatval($this->getCurrentFrom()) : null;
            $to = !empty($this->getCurrentTo()) ? floatval($this->getCurrentTo()) : null;
            if ($filterSetting->getUnitsLabelUseCurrencySymbol()) {
                $template = $this->currencySymbol . '{from} - ' . $this->currencySymbol . '{to}';
            } else {
                $template = '{from}' . $filterSetting->getUnitsLabel() . ' - {to}' . $filterSetting->getUnitsLabel();
            }

            $config =
                [
                    'from' => $from,
                    'to' => $to,
                    'min' => $min,
                    'max' => $max,
                    'requestVar' => $this->getRequestVar(),
                    'step' => round($filterSetting->getSliderStep(), 4),
                    'template' => $template,
                    'curRate' => $attrType == DisplayMode::ATTRUBUTE_PRICE ? $this->getCurrencyRate() : 1,
                ];
        }

        return $config;
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbol;
    }
}
