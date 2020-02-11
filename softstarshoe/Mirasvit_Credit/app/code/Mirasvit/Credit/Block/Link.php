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
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Credit\Block;

/**
 * Customer account dropdown link
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var string
     */
    protected $_template = 'Mirasvit_Credit::link.phtml';

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('credit/account');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Store Credit');
    }
}
