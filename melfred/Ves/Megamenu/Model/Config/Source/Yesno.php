<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Model\Config\Source;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Option\ArrayInterface;
class Yesno implements OptionSourceInterface, ArrayInterface
{


    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('No'), 1 => __('Yes')];
    }
    
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
                'label' => __('Yes'),
                'value' => 1,
            ];
        $options[] = [
                'label' => __('No'),
                'value' => 0,
            ];
        return $options;
    }
}
