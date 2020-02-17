<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 7/26/18
 * Time: 10:54 AM
 */

namespace Forix\ImportHelper\Model\Import;


use Magento\Framework\App\ResourceConnection;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

abstract class AbstractEntity  extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    /**
     * @return string
     */
    public abstract function getSourceEntityCode();

    /**
     * @param $columnName
     * @return string
     */
    public abstract function getAttributeCode($columnName);
}