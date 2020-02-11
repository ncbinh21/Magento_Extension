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



namespace Mirasvit\Credit\Block\Adminhtml\Balance;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Mirasvit\Credit\Model\BalanceFactory
     */
    protected $balanceFactory;

    /**
     * @var \Mirasvit\Credit\Helper\Renderer
     */
    protected $creditRenderer;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @param \Mirasvit\Credit\Model\BalanceFactory $balanceFactory
     * @param \Mirasvit\Credit\Helper\Renderer      $creditRenderer
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Backend\Helper\Data          $backendHelper
     * @param array                                 $data
     */
    public function __construct(
        \Mirasvit\Credit\Model\BalanceFactory $balanceFactory,
        \Mirasvit\Credit\Helper\Renderer $creditRenderer,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->balanceFactory = $balanceFactory;
        $this->creditRenderer = $creditRenderer;
        $this->context = $context;
        $this->backendHelper = $backendHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid');
        $this->setDefaultSort('balance_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->balanceFactory->create()
            ->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header' => __('Customer Name'),
            'index' => 'name',
            'filter_index' => new \Zend_Db_Expr('CONCAT(customer.firstname, " ", customer.lastname)'),
        ]);

        $this->addColumn('email', [
            'header' => __('Customer Email'),
            'index' => 'email',
        ]);

        $this->addColumn('amount', [
            'header' => __('Balance'),
            'index' => 'amount',
            'type' => 'currency',
            'currency_code' => $this->context->getStoreManager()->getStore()->getBaseCurrencyCode(),
            'frame_callback' => [$this->creditRenderer, 'amount'],
        ]);

        $this->addColumn('updated_at', [
            'header' => __('Updated At'),
            'index' => 'updated_at',
            'type' => 'datetime',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('customer/index/edit', ['id' => $row->getCustomerId()]);
    }
}
