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


namespace Mirasvit\Report\Model\Query;

use Mirasvit\Report\Api\Data\Query\RelationInterface;
use Mirasvit\Report\Api\Data\Query\TableInterface;

class Relation implements RelationInterface
{
    /**
     * @var TableInterface
     */
    public $leftTable;

    /**
     * @var TableInterface
     */
    public $rightTable;

    /**
     * @var string
     */
    protected $condition;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $additional;

    /**
     * @param TableInterface $leftTable
     * @param TableInterface $rightTable
     * @param string         $condition
     * @param string         $type
     * @param string         $additional
     */
    public function __construct(
        TableInterface $leftTable,
        TableInterface $rightTable,
        $condition,
        $type,
        $additional = ''
    ) {
        $this->leftTable = $leftTable;
        $this->rightTable = $rightTable;
        $this->condition = $condition;
        $this->type = $type;
        $this->additional = $additional;
    }

    /**
     * {@inheritdoc}
     */
    public function getLeftTable()
    {
        return $this->leftTable;
    }

    /**
     * {@inheritdoc}
     */
    public function getRightTable()
    {
        return $this->rightTable;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        $condition = str_replace('%1', $this->leftTable->getName(), $this->condition);
        $condition = str_replace('%2', $this->rightTable->getName(), $condition);

        if ('' !== $this->additional) {
            $additional = str_replace('%1', $this->leftTable->getName(), $this->additional);
            $additional = str_replace('%2', $this->rightTable->getName(), $additional);
            $condition .= $additional;
        }

        return $condition;
    }
}
