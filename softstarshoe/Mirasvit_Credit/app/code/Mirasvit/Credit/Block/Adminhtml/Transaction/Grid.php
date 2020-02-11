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



namespace Mirasvit\Credit\Block\Adminhtml\Transaction;

use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;
use Mirasvit\Credit\Model\Config\Source\Action;

class Grid extends GridExtended
{
    /**
     * @var \Mirasvit\Credit\Model\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Mirasvit\Credit\Helper\Renderer
     */
    protected $creditRenderer;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var Action
     */
    protected $action;

    /**
     * @param \Mirasvit\Credit\Model\TransactionFactory $earningFactory
     * @param \Mirasvit\Credit\Helper\Renderer          $creditRenderer
     * @param  Action                                   $action
     * @param \Magento\Backend\Block\Widget\Context     $context
     * @param \Magento\Backend\Helper\Data              $backendHelper
     */
    public function __construct(
        \Mirasvit\Credit\Model\TransactionFactory $earningFactory,
        \Mirasvit\Credit\Helper\Renderer $creditRenderer,
        Action $action,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper
    ) {
        $this->transactionFactory = $earningFactory;
        $this->creditRenderer = $creditRenderer;
        $this->action = $action;
        $this->context = $context;

        parent::__construct($context, $backendHelper);

        $this->addExportType('credit/transaction/export/type/csv', 'csv');
        $this->addExportType('credit/transaction/export/type/xml', 'xml');
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('credit_transaction_grid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->transactionFactory->create()
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
        $this->addColumn('transaction_id', [
            'header'        => __('Transaction #'),
            'header_export' => 'transaction_id',
            'type'          => 'number',
            'index'         => 'transaction_id'
        ]);

        $this->addColumn('name', [
            'header'         => __('Customer Name'),
            'header_export'  => 'customer_name',
            'index'          => 'name',
            'filter_index'   => new \Zend_Db_Expr('CONCAT(customer.firstname, " ", customer.lastname)'),
            'frame_callback' => [$this->creditRenderer, 'customerName'],
        ]);

        $this->addColumn('email', [
            'header'        => __('Customer Email'),
            'header_export' => 'customer_email',
            'index'         => 'email',
        ]);

        $this->addColumn('updated_at', [
            'header'        => __('Date'),
            'header_export' => 'updated_at',
            'index'         => 'updated_at',
            'filter_index'  => 'main_table.updated_at',
            'type'          => 'datetime',
        ]);

        $this->addColumn('balance_delta', [
            'header'         => __('Balance Change'),
            'header_export'  => 'balance_delta',
            'index'          => 'balance_delta',
            'currency_code'  => $this->context->getStoreManager()->getStore()->getBaseCurrencyCode(),
            'type'           => $this->_isExport ? 'text' : 'currency',
            'frame_callback' => [$this->creditRenderer, 'amountDelta'],
        ]);

        $this->addColumn('balance_amount', [
            'header'         => __('Balance'),
            'header_export'  => 'balance_amount',
            'index'          => 'balance_amount',
            'type'           => $this->_isExport ? 'text' : 'currency',
            'currency_code'  => $this->context->getStoreManager()->getStore()->getBaseCurrencyCode(),
            'frame_callback' => [$this->creditRenderer, 'amount'],
        ]);

        $this->addColumn('action', [
            'header'        => __('Action'),
            'header_export' => 'action',
            'index'         => 'action',
            'type'          => 'options',
            'filter_index'  => 'main_table.action',
            'options'       => $this->action->toOptionHash(),
        ]);

        $this->addColumn('message', [
            'header'         => __('Additional Message'),
            'header_export'  => 'message',
            'index'          => 'message',
            'filter_index'   => 'main_table.message',
            'frame_callback' => [$this->creditRenderer, 'transactionMessage'],
        ]);

        $this->addColumn('is_notified', [
            'header'        => __('Is Notified?'),
            'header_export' => 'is_notified',
            'index'         => 'is_notified',
            'filter_index'  => 'main_table.is_notified',
            'width'         => '60px',
            'type'          => 'options',
            'options'       => [
                1 => __('Yes'),
                0 => __('No'),
            ],
        ]);

        if ($this->_isExport) {
            $this->addColumn('website_id', [
                'header_export'  => 'website_id',
                'index'          => 'website_id',
                'frame_callback' => [$this->creditRenderer, 'websiteCode'],
            ]);
        }

        return parent::_prepareColumns();
    }

    /**
     * Retrieve a file container array by grid data as XML
     *
     * Return array with keys type and value
     *
     * @return array
     */
    public function getXmlFile()
    {
        $this->_isExport = true;
        $this->_prepareGrid();

        $name = md5(microtime());
        $file = $this->_path . '/' . $name . '.xml';

        $this->_directory->create($this->_path);
        $stream = $this->_directory->openFile($file, 'w+');

        $stream->lock();
        $stream->write($this->getXml());

        $stream->unlock();
        $stream->close();

        return [
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true  // can delete file after use
        ];
    }
}
