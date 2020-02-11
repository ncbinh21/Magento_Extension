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
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Block\Adminhtml\Queue;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class View extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Constructor
     *
     * @param Registry $registry
     * @param Context  $context
     */
    public function __construct(
        Registry $registry,
        Context $context
    ) {
        $this->registry = $registry;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function _prepareLayout()
    {
        $this->setTemplate('queue/view.phtml');
        $this->getToolbar()->addChild(
            'back_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/index') . '\')',
                'class' => 'back'
            ]
        );
        $this->getToolbar()->addChild(
            'cancel_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Cancel'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/cancel', ['_current' => true]) . '\')',
                'class' => 'delete'
            ]
        );
        $this->getToolbar()->addChild(
            'reset_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Reset'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/reset', ['_current' => true]) . '\')',
                'class' => 'delete'
            ]
        );
        $this->getToolbar()->addChild(
            'send_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Send'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/send', ['_current' => true]) . '\')',
                'class' => 'save primary'
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * Current queue model
     *
     * @return \Mirasvit\Email\Model\Queue
     */
    public function getModel()
    {
        return $this->registry->registry('current_model');
    }

    /**
     * Email preview url
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/drop', ['_current' => true]);
    }
}
