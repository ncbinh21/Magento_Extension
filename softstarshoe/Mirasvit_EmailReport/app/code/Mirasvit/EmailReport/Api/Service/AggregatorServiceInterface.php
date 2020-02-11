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
 * @package   mirasvit/module-email-report
 * @version   1.0.5
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Api\Service;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

interface AggregatorServiceInterface
{
    /**
     * Count number of records in the $collection where $field is equal to $fieldId.
     *
     * @param AbstractCollection $collection
     * @param string             $field
     * @param int                $fieldId
     *
     * @return int
     */
    public function count(AbstractCollection $collection, $field, $fieldId);
}
