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



namespace Mirasvit\Credit\Block\Adminhtml\Customer\Edit\Tab\Credit;

use Magento\Customer\Controller\RegistryConstants;
use Mirasvit\Credit\Model\Config\Source\Action;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Mirasvit\Credit\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Mirasvit\Credit\Helper\Renderer
     */
    protected $creditRenderer;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var Action
     */
    protected $action;

    /**
     * @param \Mirasvit\Credit\Model\ResourceModel\Transaction\CollectionFactory $earningCollectionFactory
     * @param \Mirasvit\Credit\Helper\Renderer                                   $creditRenderer
     * @param Action                                                             $action
     * @param \Magento\Framework\Registry                                        $registry
     * @param \Magento\Backend\Block\Widget\Context                              $context
     * @param \Magento\Backend\Helper\Data                                       $backendMessageHelper
     */
    public function __construct(
        \Mirasvit\Credit\Model\ResourceModel\Transaction\CollectionFactory $earningCollectionFactory,
        \Mirasvit\Credit\Helper\Renderer $creditRenderer,
        Action $action,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendMessageHelper
    ) {
        $this->transactionCollectionFactory = $earningCollectionFactory;
        $this->creditRenderer = $creditRenderer;
        $this->action = $action;
        $this->registry = $registry;
        $this->context = $context;

        parent::__construct($context, $backendMessageHelper);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setDefaultSort('updated_at');
        $this->setDefaultDir('desc');

        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setDefaultLimit(100);

        $this->setEmptyText(__('No Transactions Found'));
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->transactionCollectionFactory->create()
            ->addFilterByCustomer($this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', [
            'header'   => __('Transaction #'),
            'type'     => 'number',
            'index'    => 'transaction_id',
            'width'    => '50px',
            'sortable' => false,
        ]);

        $this->addColumn('updated_at', [
            'header'   => __('Date'),
            'index'    => 'updated_at',
            'type'     => 'datetime',
            'sortable' => false,
        ]);

        $this->addColumn('balance_delta', [
            'header'         => __('Balance Change'),
            'index'          => 'balance_delta',
            'type'           => 'currency',
            'currency_code'  => $this->context->getStoreManager()->getStore()->getBaseCurrencyCode(),
            'frame_callback' => [$this->creditRenderer, 'amountDelta'],
            'sortable'       => false,
        ]);

        $this->addColumn('balance_amount', [
            'header'         => __('Balance'),
            'index'          => 'balance_amount',
            'type'           => 'currency',
            'currency_code'  => $this->context->getStoreManager()->getStore()->getBaseCurrencyCode(),
            'frame_callback' => [$this->creditRenderer, 'amount'],
            'sortable'       => false,
        ]);

        $this->addColumn('action', [
            'header'       => __('Action'),
            'index'        => 'action',
            'filter_index' => 'main_table.action',
            'sortable'     => false,
            'type'         => 'options',
            'options'      => $this->action->toOptionHash(),
        ]);

        $this->addColumn('message', [
            'header'         => __('Additional Message'),
            'index'          => 'message',
            'filter_index'   => 'main_table.message',
            'sortable'       => false,
            'frame_callback' => [$this->creditRenderer, 'transactionMessage'],
        ]);

        return parent::_prepareColumns();
    }
}
