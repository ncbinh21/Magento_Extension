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


namespace Mirasvit\Credit\Reports;

use Mirasvit\Report\Model\AbstractReport;
use Mirasvit\Report\Model\Select\Column;

class Overview extends AbstractReport
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Store Credit');
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'credit_overview';
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setBaseTable('mst_credit_transaction');

        $this->addFastFilters([
            'mst_credit_transaction|created_at',
        ]);

        $this->setDefaultColumns([
            'mst_credit_transaction|sum_balance_delta',
            'mst_credit_transaction|sum_positive_balance_delta',
            'mst_credit_transaction|sum_negative_balance_delta',
            'mst_credit_transaction|quantity',
        ]);

        $this->setDefaultDimension('mst_credit_transaction|day');

        $this->addAvailableDimensions([
            'mst_credit_transaction|day',
            'mst_credit_transaction|week',
            'mst_credit_transaction|month',
            'mst_credit_transaction|year',
        ]);

        $this->setGridConfig([
            'paging' => true,
        ]);

        $this->setChartConfig([
            'chartType' => 'column',
            'vAxis'     => 'mst_credit_transaction|sum_balance_delta',
        ]);
    }
}
