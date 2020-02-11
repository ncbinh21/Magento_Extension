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



namespace Mirasvit\Credit\Block\Adminhtml\Transaction;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Magento\Cms\Model\Wysiwyg\Config     $wysiwygConfig
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'transaction_id';
        $this->_controller = 'adminhtml_transaction';
        $this->_blockGroup = 'Mirasvit_Credit';

        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->update('delete', 'label', __('Delete'));

        return $this;
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->wysiwygConfig->isEnabled()) {
        }
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();

        if (!$this->registry->registry('current_transaction')->getCustomerId()) {
            $html .= $this->getLayout()
                ->createBlock('\Mirasvit\Credit\Block\Adminhtml\Transaction\Edit\Customer')->toHtml();
        }

        return $html;
    }

    /**
     * @return \Mirasvit\Credit\Model\ResourceModel\Transaction\
     */
    public function getTransaction()
    {
        if (
            $this->registry->registry('current_transaction') &&
            $this->registry->registry('current_transaction')->getId()
        ) {
            return $this->registry->registry('current_transaction');
        }
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($transaction = $this->getTransaction()) {
            return __("Edit Transaction '%1'", $this->escapeHtml($transaction->getName()));
        } else {
            return __('Create New Transaction');
        }
    }
}
