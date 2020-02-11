<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Block\Adminhtml\Status\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Email extends Generic implements TabInterface
{
    protected $_systemStore;
    protected $_groupRepository;
    protected $_searchCriteriaBuilder;
    protected $_objectConverter;
    protected $_objectManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Convert\DataObject $objectConverter,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    )
    {
        $this->_groupRepository = $groupRepository;
        $this->_systemStore = $systemStore;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_objectConverter = $objectConverter;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getTabLabel()
    {
        return __('E-mail Notifications');
    }

    public function getTabTitle()
    {
        return __('E-mail Notifications');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_order_status');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('amostatus_');
        $fieldsetNotifications = $form->addFieldset('notifications_fieldset', ['legend' => __('Enable Notifications')]);

        $fieldsetNotifications->addField(
            'notify_by_email',
            'select',
            [
                'name' => 'notify_by_email',
                'label' => __('Always Notify Customer By E-mail'),
                'title' => __('Always Notify Customer By E-mail'),
                'options' => [0 => __('No'), 1 => __('Yes')],
                'note' => __('If set to `Yes`, customer always gets e-mail notification when order status is set to the current one')
            ]
        );

        $fieldsetStoreviewEmailTemplate = $form->addFieldset('store_view_email_template_fieldset', ['legend' => __('Store View E-mail Template')]);

        $storeViews = $this->_storeManager->getStores();

        /** @var \Magento\Email\Model\ResourceModel\Template\Collection $optionsModel */
        $optionsModel = $this->_objectManager->get('Magento\Email\Model\ResourceModel\Template\Collection');
        $options = $this->_toOptions($optionsModel);
        $options[0] = __('Order Status Change (Default Template From Locale)');

        foreach ($storeViews as $storeView) {
            $fieldsetStoreviewEmailTemplate->addField(
                'store_template[' . $storeView->getStoreId() . ']',
                'select',
                [
                    'name' => 'store_template[' . $storeView->getStoreId() . ']',
                    'label' => '"' . $storeView->getName() . '" ' . __('Store Template'),
                    'title' => __('Store Template'),
                    'options' => $options
                ]
            );
        }

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _toOptions($collection)
    {
        $options = [];

        foreach ($collection as $item) {
            $options[$item->getTemplateId()] = $item->getTemplateCode();
        }

        return $options;
    }
}
