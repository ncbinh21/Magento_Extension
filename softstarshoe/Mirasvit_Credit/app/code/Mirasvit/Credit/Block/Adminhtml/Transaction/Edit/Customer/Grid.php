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



namespace Mirasvit\Credit\Block\Adminhtml\Transaction\Edit\Customer;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\User\Model\ResourceModel\Role\User\Collection
     */
    protected $rolesFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @param \Magento\User\Model\ResourceModel\Role\User\CollectionFactory    $rolesFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Framework\Registry                                      $registry
     * @param \Magento\Framework\Json\Helper\Data                              $jsonEncoder
     * @param \Magento\Backend\Block\Widget\Context                            $context
     * @param \Magento\Backend\Helper\Data                                     $backendMessageHelper
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\User\Model\ResourceModel\Role\User\CollectionFactory  $rolesFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\Helper\Data $jsonEncoder,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendMessageHelper,
        array $data = []
    ) {
        $this->rolesFactory = $rolesFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->context = $context;
        $this->backendHelper = $backendMessageHelper;
        parent::__construct($context, $backendMessageHelper, $data);
    }

    /**
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('credit_transaction_edit_customer_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareCollection()
    {
        $collection = $this->customerCollectionFactory->create()
            ->addNameToSelect()
            ->addAttributeToSelect('email');

        if (
            $this->registry->registry('current_transaction') &&
            $this->registry->registry('current_transaction')->getCustomerId() > 0
        ) {
            $collection->addFieldToFilter(
                'entity_id',
                $this->registry->registry('current_transaction')->getCustomerId()
            );
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header' => __('ID'),
            'width' => '50px',
            'index' => 'entity_id',
            'align' => 'right',
        ]);

        $this->addColumn('name', [
            'header' => __('Name'),
            'index' => 'name',
        ]);

        $this->addColumn('email', [
            'header' => __('Email'),
            'index' => 'email',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('customer_id');

        $this->getMassactionBlock()->addItem('select', [
            'label' => __('Select'),
        ]);

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/loadCustomerBlock', ['block' => 'customer_grid']);
    }

    /**
     * @param bool|false $json
     * @return array|string
     */
    protected function _getUsers($json = false)
    {
        if ($this->getRequest()->getParam('in_role_user') != '') {
            return $this->getRequest()->getParam('in_role_user');
        }

        $roleId = $this->getRequest()->getParam('rid') > 0
            ? $this->getRequest()->getParam('rid')
            : $this->registry->registry('RID');

        $users = $this->rolesFactory->create()->setId($roleId)->getRoleUsers();

        if (sizeof($users) > 0) {
            if ($json) {
                $jsonUsers = [];

                foreach ($users as $usrid) {
                    $jsonUsers[$usrid] = 0;
                }

                return $this->jsonEncoder->jsonEncode((object) $jsonUsers);
            } else {
                return array_values($users);
            }
        } else {
            if ($json) {
                return '{}';
            } else {
                return [];
            }
        }
    }
}
