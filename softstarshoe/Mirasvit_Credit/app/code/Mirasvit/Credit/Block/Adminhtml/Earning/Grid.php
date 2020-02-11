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



namespace Mirasvit\Credit\Block\Adminhtml\Earning;

use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;
use Mirasvit\Credit\Model\Config\Source\Action;

class Grid extends GridExtended
{
    /**
     * @var \Mirasvit\Credit\Model\EarningFactory
     */
    protected $earningFactory;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var Action
     */
    protected $action;

    public function __construct(
        \Mirasvit\Credit\Model\EarningFactory $earningFactory,
        Action $action,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper
    ) {
        $this->earningFactory = $earningFactory;
        $this->action = $action;
        $this->context = $context;

        parent::__construct($context, $backendHelper);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('credit_earning_grid');
        $this->setDefaultSort('earning_rule_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->earningFactory->create()
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
        $this->addColumn('earning_rule_id', [
            'header' => __('#'),
            'type'   => 'number',
            'index'  => 'earning_rule_id',
        ]);

        $this->addColumn('name', [
            'header' => __('Rule Name'),
            'index'  => 'name',
        ]);

        $this->addColumn('is_active', [
                'header'       => __('Is Active'),
                'index'        => 'is_active',
                'filter_index' => 'is_active',
                'type'         => 'options',
                'options'      => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
