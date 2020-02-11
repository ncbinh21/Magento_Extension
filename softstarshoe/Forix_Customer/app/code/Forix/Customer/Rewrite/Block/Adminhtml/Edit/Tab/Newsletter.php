<?php

namespace Forix\Customer\Rewrite\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;

class Newsletter extends \Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Customer::tab/newsletter.phtml';
    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Subscriptions');
    }

    /**
     * Return Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Subscriptions');
    }
    /**
     * Initialize the form.
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function initForm()
    {
        if (!$this->canShowTab()) {
            return $this;
        }
        /**@var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('_newsletter');
        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        $subscriber = $this->_subscriberFactory->create()->loadByCustomerId($customerId);
        $this->_coreRegistry->register('subscriber', $subscriber, true);

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Newsletter Information')]);

        $fieldset->addField(
            'subscription',
            'checkbox',
            [
                'label' => __('Subscribed to Softstar Review requests and shopping reminders'),
                'name' => 'subscription',
                'value' => 'true',
                'data-form-part' => $this->getData('target_form'),
                'onchange' => 'this.value = this.checked;'
            ]
        );

        if ($this->customerAccountManagement->isReadOnly($customerId)) {
            $form->getElement('subscription')->setReadonly(true, true);
        }
        $isSubscribed = $subscriber->isSubscribed();
        $form->setValues(['subscription' => $isSubscribed ? 'true' : 'false']);
        $form->getElement('subscription')->setIsChecked($isSubscribed);

        $this->updateFromSession($form, $customerId);

        $changedDate = $this->getStatusChangedDate();
        if ($changedDate) {
            $fieldset->addField(
                'change_status_date',
                'label',
                [
                    'label' => $isSubscribed ? __('Last Date Subscribed') : __('Last Date Unsubscribed'),
                    'value' => $changedDate,
                    'bold' => true
                ]
            );
        }

        $this->setForm($form);
        return $this;
    }
}