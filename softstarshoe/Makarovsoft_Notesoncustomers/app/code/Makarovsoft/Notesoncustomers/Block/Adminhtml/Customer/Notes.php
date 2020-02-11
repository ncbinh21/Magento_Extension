<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Makarovsoft\Notesoncustomers\Block\Adminhtml\Customer;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Customer account form block
 */
class Notes extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Makarovsoft\Notesoncustomers\Model\NotesFactory
     */
    protected $notesFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Makarovsoft\Notesoncustomers\Model\NotesFactory $notesFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->notesFactory = $notesFactory;
        parent::__construct($context, $data);
    }

    public function getCollection()
    {
        $collection = $this->notesFactory
            ->create()
            ->getCollection()
            ->setOrder('updated_at', 'desc');

        $collection->addFieldToFilter('customer_id', $this->getCustomerId());

        $collection->getSelect()->joinLeft(
            array('user'=>$collection->getTable('admin_user')),
            'user.user_id=main_table.user_id',
            array('firstname', 'lastname')
        );

        return $collection;
    }

    public function getIsOrder()
    {
        return false;
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->getRequest()->getParam('id');
    }
}
