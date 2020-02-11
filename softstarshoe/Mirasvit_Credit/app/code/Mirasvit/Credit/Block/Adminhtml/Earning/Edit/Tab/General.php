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



namespace Mirasvit\Credit\Block\Adminhtml\Earning\Edit\Tab;

use Magento\Backend\Block\Widget\Form;

class General extends Form
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var \Magento\Config\Model\Config\Source\Website
     */
    protected $sourceStore;

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

    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \Magento\Config\Model\Config\Source\Store $sourceStore,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->sourceStore = $sourceStore;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);

        /** @var \Mirasvit\Credit\Model\Earning $model */
        $model = $this->registry->registry('current_earning');

        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);
        if ($model->getId()) {
            $fieldset->addField('earning_rule_id', 'hidden', [
                'name'  => 'earning_rule_id',
                'value' => $model->getId(),
            ]);
        }

        $fieldset->addField('name', 'text', [
            'label'    => __('Rule Name'),
            'required' => true,
            'name'     => 'name',
            'value'    => $model->getName(),
        ]);

        $fieldset->addField('is_active', 'select', [
            'label'    => __('Is Active'),
            'required' => true,
            'name'     => 'is_active',
            'value'    => $model->getIsActive(),
            'values'   => [0 => __('No'), 1 => __('Yes')],
        ]);


        $stores = $this->sourceStore->toOptionArray();
        if (!$this->context->getStoreManager()->isSingleStoreMode() && count($stores) > 1) {
            $fieldset->addField('store_ids', 'multiselect', [
                'name'     => 'store_ids[]',
                'label'    => __('Stores'),
                'required' => true,
                'values'   => $stores,
                'value'    => $model->getStoreIds(),
            ]);
        } else {
            $fieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids',
                'value' => $this->context->getStoreManager()->getStore(true)->getId(),
            ]);
            $model->setStoreIds($this->context->getStoreManager()->getStore(true)->getId());
        }

        $fieldset->addField('group_ids', 'multiselect', [
            'label'    => __('Customer Groups'),
            'required' => true,
            'name'     => 'group_ids[]',
            'value'    => $model->getGroupIds(),
            'values'   => $this->groupCollectionFactory->create()->toOptionArray(),
        ]);

        return parent::_prepareForm();
    }
}
