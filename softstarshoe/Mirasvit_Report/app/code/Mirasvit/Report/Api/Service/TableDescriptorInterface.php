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


interface TableDescriptorInterface
{
    const TMP_TABLE_SUFFIX = '_aggregated';

    /**
     * Describe database scheme.
     *
     * @return string[]
     */
    public function describeTable();

    /**
     * Retrieve records from the table.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return array - result
     */
    public function fetchAll($offset, $limit);
}
