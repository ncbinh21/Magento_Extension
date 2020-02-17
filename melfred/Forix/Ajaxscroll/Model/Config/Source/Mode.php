<?php
/*************************************************
 * *
 *  *
 *  * @copyright Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *  * @author    thao@forixwebdesign.com
 *
 */
namespace Forix\Ajaxscroll\Model\Config\Source;

/**
 * Class Mode
 *
 * @package Forix\Ajaxscroll\Model\Config\Source
 */
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Mode extends AbstractSource
{
    /**@#%
     * @const
     */
    const CLICK_TO_LOAD_MORE_MODE = 1;
    const AUTO_LOADING_WHEN_SCROLL = 2;

    /**
     * @var array $options
     */
    protected $options;

    /**
     * Get Options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->options == null) {
            $this->options = [
                ['value' => self::CLICK_TO_LOAD_MORE_MODE, 'label' => __('Click button to load more')],
                ['value' => self::AUTO_LOADING_WHEN_SCROLL, 'label' => __('Autoload when scroll')],
            ];
        }

        return $this->options;
    }
}