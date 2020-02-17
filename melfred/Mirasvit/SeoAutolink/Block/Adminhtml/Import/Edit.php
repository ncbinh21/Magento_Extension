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



namespace Mirasvit\SeoAutolink\Block\Adminhtml\Import;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @return $this
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'import';
        $this->_controller = 'adminhtml_import';
        $this->_blockGroup = 'Mirasvit_SeoAutolink';
        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');

        $this->addButton(
            'save',
            [
                'label' => __('Run Import'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']],
                ]
            ],
            1
        );
        return $this;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Import Auto Links');
    }
}
