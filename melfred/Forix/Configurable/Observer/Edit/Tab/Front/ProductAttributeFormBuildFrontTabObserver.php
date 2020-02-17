<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 08/08/2018
 * Time: 10:53
 */

namespace Forix\Configurable\Observer\Edit\Tab\Front;

use Magento\Framework\Module\Manager;
use Magento\Framework\Event\ObserverInterface;

class ProductAttributeFormBuildFrontTabObserver implements ObserverInterface
{
    /**
     * @var \Forix\Configurable\Model\Attribute\Source\AttributeTemplate
     */
    protected $optionList;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param Manager $moduleManager
     * @param \Forix\Configurable\Model\Attribute\Source\AttributeTemplate $optionList
     */
    public function __construct(Manager $moduleManager, \Forix\Configurable\Model\Attribute\Source\AttributeTemplate $optionList)
    {
        $this->optionList = $optionList;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->moduleManager->isOutputEnabled('Forix_Configurable')) {
            return;
        }

        /** @var \Magento\Framework\Data\Form\AbstractForm $form */
        $form = $observer->getForm();

        $fieldset = $form->getElement('front_fieldset');

        $fieldset->addField(
            'option_template',
            'select',
            [
                'name' => 'option_template',
                'label' => __("Use in Configurable Product View"),
                'title' => __('Can be used only with catalog input type Dropdown'),
                'note' => __('Can be used only with catalog input type Dropdown'),
                'values' => $this->optionList->toOptionArray(),
            ]
        );
    }
}
