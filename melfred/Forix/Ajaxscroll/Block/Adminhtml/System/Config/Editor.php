<?php
/*************************************************
 * *
 *  *
 *  * @copyright Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *  * @author    thao@forixwebdesign.com
 *
 */
namespace Forix\Ajaxscroll\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field as FormField;

/**
 * Class Editor
 *
 * @package Forix\NewsletterPopup\Block\Adminhtml\System\Config
 */
class Editor extends FormField
{
    /**
     * @var WysiwygConfig
     */
    protected $_wysiwygConfig;

    /**
     * Editor constructor.
     *
     * @param Context $context
     * @param WysiwygConfig $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setWysiwyg(true);
        $element->setConfig($this->_wysiwygConfig->getConfig(
            [
                'width' => '40%',
                'height' => '300px',
                'add_variables' => false,
                'add_widgets' => false,
                'hidden' => true,
            ])
        );
        return parent::_getElementHtml($element);
    }
}