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



namespace Mirasvit\Credit\Block\Adminhtml\Transaction\Edit;

class Form extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Backend\Block\Widget\Context   $context
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create()->setData([
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ]);

        $transaction = $this->registry->registry('current_transaction');

        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);

        if ($transaction->getId()) {
            $fieldset->addField('transaction_id', 'hidden', [
                'name'  => 'transaction_id',
                'value' => $transaction->getId(),
            ]);
        }

        $customerFieldset = $form->addFieldset('customer_id_set', []);
        $customerFieldset->addField('customer_id', 'hidden', [
            'label'               => __('Customer ID'),
            'required'            => true,
            'name'                => 'customer_id',
            'value'               => $transaction->getCustomerId(),
            'class'               => 'admin__field',
            'before_element_html' => '<div>',
            'after_element_html'  => '<br></div>',
        ]);

        if ($transaction->getCustomerId() > 0) {
            $customer = $this->customerFactory->create()->load($transaction->getCustomerId());

            $fieldset->addField('customer_name', 'label', [
                'label' => __('Customer'),
                'value' => $customer->getFirstname()
                    . ' '
                    . $customer->getLastname()
                    . ' <'
                    . $customer->getEmail()
                    . '>',
            ]);
        }

        $fieldset->addField('balance_delta', 'text', [
            'label'            => __('Store Credit Balance Change'),
            'required'         => true,
            'name'             => 'balance_delta',
            'value'            => $transaction->getBalanceDelta(),
            'class'            => 'validate-balance',
            'after_element_js' => '
                <script>
                require([
                        "jquery",
                        "Magento_Ui/js/lib/validation/utils",
                        "mage/backend/validation",
                    ], function($, utils){
                        $.validator.addMethod(
                            "validate-balance", function (value) {
                                return utils.isEmptyNoTrim(value) ||
                                    (!isNaN(utils.parseNumber(value)) && /^\s*[-+]?\d*(\.\d*)?\s*$/.test(value));
                            }, $.mage.__("Select type of option.")
                        );
                    }
                );
                </script>'
        ]);

        $fieldset->addField('message', 'text', [
            'label'    => __('Additional Message'),
            'required' => true,
            'name'     => 'message',
            'value'    => $transaction->getMessage(),
        ]);

        if ($this->registry->registry('referrer_url')) {
            $fieldset->addField('referrer_url', 'hidden', [
                'name'  => 'referrer_url',
                'value' => $this->registry->registry('referrer_url')
            ]);
        }


        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
