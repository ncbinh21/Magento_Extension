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

use Mirasvit\Report\Api\Data\Query\FieldInterface;
use Mirasvit\Report\Api\Data\Query\TableInterface;

class Field implements FieldInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var TableInterface
     */
    protected $table;

    /**
     * @param TableInterface $table
     * @param string         $name
     */
    public function __construct(
        TableInterface $table,
        $name
    ) {
        $this->table = $table;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function toDbExpr()
    {
        return $this->table->getName() . '.' . $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function join($select)
    {
        $select->joinTable($this->getTable());

        return $this;
    }
}
