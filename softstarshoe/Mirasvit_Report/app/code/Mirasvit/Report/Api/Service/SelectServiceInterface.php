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
 * @package   mirasvit/module-report
 * @version   1.2.27
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Report\Api\Service;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Mirasvit\Report\Api\Data\Query\TableInterface;

interface SelectServiceInterface
{
    /**
     * @param TableInterface  $table
     * @param  TableInterface $baseTable
     * @return bool
     */
    public function replicateTable(TableInterface $table, TableInterface $baseTable);

    /**
     * @param AdapterInterface $connection
     * @return $this
     */
    public function applyTimeZone(AdapterInterface $connection);

    /**
     * @param AdapterInterface $connection
     * @return $this
     */
    public function restoreTimeZone(AdapterInterface $connection);
}