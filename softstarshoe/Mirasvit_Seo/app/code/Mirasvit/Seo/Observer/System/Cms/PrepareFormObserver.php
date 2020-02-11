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
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Observer\System\Cms;

use Magento\Framework\Event\ObserverInterface;

class PrepareFormObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param string $observer
     *
     * @return void
     */
    public function prepareForm($observer)
    {
        $model = $this->registry->registry('cms_page');
        $form = $observer->getForm();
        $fieldset = $form->addFieldset(
            'seo_alternate_fieldset',
            ['legend' => __('Alternate Settings'), 'class' => 'input-text']
        );
        $fieldset->addField('alternate_group', 'text', [
            'name' => 'alternate_group',
            'label' => __('Alternate group'),
            'title' => __('Alternate group'),
            'disabled' => false,
            'value' => $model->getAlternateGroup(),
        ]);
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->prepareForm($observer);
    }
}
