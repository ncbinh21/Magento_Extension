<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Helper;

class Parse extends \Magento\Framework\App\Helper\AbstractHelper
{
    const WIDGET_BLOCK_WRAPPER = '+++!!!________________________!!!+++';

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var null|string
     */
    protected $constructions;

    /**
     * @param \Magento\Framework\App\Helper\Context  $context
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Eav\Model\Config              $eavConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->context = $context;
        $this->pricingHelper = $pricingHelper;
        $this->eavConfig = $eavConfig;
        parent::__construct($context);
    }

    /**
     * Parse string.
     * e.g. of str
     * [product_name][, model: {product_model}!] [product_nonexists]  [buy it {product_nonexists} !]
     *
     * @param string   $str
     * @param string   $objects
     * @param array    $additional
     * @param int|bool $storeId
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function parse($str, $objects, $additional = [], $storeId = false)
    {
        if (trim($str) == '') {
            return $str;
        }

        $str = $this->parseWidget($str);

        $bAOpen = '[ZZZZZ';
        $bAClose = 'ZZZZZ]';
        $bBOpen = '{WWWWW';
        $bBClose = 'WWWWW}';

        $str = str_replace('[', $bAOpen, $str);
        $str = str_replace(']', $bAClose, $str);
        $str = str_replace('{', $bBOpen, $str);
        $str = str_replace('}', $bBClose, $str);

        $pattern = '/\[ZZZZZ[^ZZZZZ\]]*ZZZZZ\]/';

        preg_match_all($pattern, $str, $matches, PREG_SET_ORDER);

        $vars = [];
        foreach ($matches as $matche) {
            $vars[$matche[0]] = $matche[0];
        }

        foreach ($objects as $key => $object) {
            $data = $object->getData();
            if (isset($additional[$key])) {
                $data = array_merge($data, $additional[$key]);
            }

            foreach ($data as $dataKey => $value) {
                if (is_array($value) || is_object($value)) {
                    continue;
                }

                $kA = $bBOpen.$key.'_'.$dataKey.$bBClose;
                $kB = $bAOpen.$key.'_'.$dataKey.$bAClose;
                $skip = true;

                foreach ($vars as $k => $v) {
                    if (stripos($v, $kA) !== false || stripos($v, $kB) !== false) {
                        $skip = false;
                        break;
                    }
                }

                if ($skip) {
                    continue;
                }

                $value = $this->checkForConvert($object, $key, $dataKey, $value, $storeId);
                foreach ($vars as $k => $v) {
                    if ($value == '') {
                        if (stripos($v, $kA) !== false || stripos($v, $kB) !== false) {
                            $vars[$k] = '';
                            continue;
                        }
                    }

                    $v = str_replace($kA, $value, $v);
                    $v = str_replace($kB, $value, $v);
                    $vars[$k] = $v;
                }
            }
        }

        foreach ($vars as $k => $v) {
            //if no attibute like [product_nonexists]
            if ($v == $k) {
                $v = '';
            }

            //remove start and end symbols from the string (trim)
            if (substr($v, 0, strlen($bAOpen)) == $bAOpen) {
                $v = substr($v, strlen($bAOpen), strlen($v));
            }

            if (strpos($v, $bAClose) === strlen($v) - strlen($bAClose)) {
                $v = substr($v, 0, strlen($v) - strlen($bAClose));
            }

            //if no attibute like [buy it {product_nonexists} !]
            if (stripos($v, $bBOpen) !== false || stripos($v, $bAOpen) !== false) {
                $v = '';
            }

            $str = str_replace($k, $v, $str);
        }


        $str = $this->restoreWidgetBlocks($str);

        return $str;
    }

    /**
     * @param string $str
     * @return string
     */
    protected function parseWidget($str)
    {
        if (preg_match_all(\Magento\Framework\Filter\Template::CONSTRUCTION_PATTERN,
            $str,
            $constructions,
            PREG_SET_ORDER)
        ) {
            $this->constructions = $constructions;
            foreach ($constructions as $key => $construction) {
                $str = str_replace($construction[0],
                    self::WIDGET_BLOCK_WRAPPER . $key . self::WIDGET_BLOCK_WRAPPER,
                    $str
                );
            }
        }

        return $str;
    }

    /**
     * @param string $str
     * @return string
     */
    protected function restoreWidgetBlocks($str)
    {
        if ($this->constructions) {
            foreach ($this->constructions as $key => $construction) {
                $str = str_replace(self::WIDGET_BLOCK_WRAPPER . $key . self::WIDGET_BLOCK_WRAPPER,
                    $construction[0],
                    $str
                );
            }
            $this->constructions = null;
        }

        return $str;
    }

    /**
     * @param string $object
     * @param string $key
     * @param string $dataKey
     * @param string $value
     * @param string $storeId
     * @return float|string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function checkForConvert($object, $key, $dataKey, $value, $storeId)
    {
        if ($key == 'product' || $key == 'category') {
            if ($key == 'product') {
                $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $dataKey);
            } else {
                $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Category::ENTITY, $dataKey);
            }

            if ($storeId) {
                if (is_object($storeId) && $storeId->getStoreId()) {
                    $storeId = $storeId->getStoreId();
                }
                $attribute->setStoreId($storeId);
            }

            if ($attribute->getId() > 0) {
                try {
                    $valueId = $object->getDataUsingMethod($dataKey);
                    $value = $attribute->getFrontend()->getValue($object);
                } catch (\Exception $e) {
                    //possible that some extension is removed, but we have it attribute with source in database
                    $value = '';
                }

                if (!$value) { //need for manufacturer
                    if (is_object($object->getResource())) {
                        try {
                            $value = $object->getResource()->getAttribute($dataKey)->getFrontend()->getValue($object);
                        } catch (\Exception $e) {
                            $value = '';
                        }
                    }
                }

                if ($value == 'No' && $valueId == '') {
                    $value = '';
                }

                switch ($dataKey) {
                    case 'price':
                        $value = $this->pricingHelper->currency($value, true, false);
                        break;
                    case 'special_price':
                        $value = $this->pricingHelper->currency($value, true, false);
                        break;
                }
            } else {
                switch ($dataKey) {
                    case 'final_price':
                        $value = $this->pricingHelper->currency($value, true, false);
                        break;
                }
            }
        }

        if (is_array($value)) {
            if (isset($value['label'])) {
                $value = $value['label'];
            } else {
                $value = '';
            }
        }

        return $value;
    }
}
