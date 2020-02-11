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



namespace Mirasvit\Report\Model\Query\Column;


use Mirasvit\Report\Model\Query\Column;

class Product extends Column
{
    /**
     * {@inheritDoc}
     */
    public function filter(\Mirasvit\Report\Ui\DataProvider $dataProvider, $value)
    {
        if (isset($value['from'])) {
            $this->filterBuilder
                ->setField($this->getName())
                ->setValue($value['from'])
                ->setConditionType('from');

            $dataProvider->addFilter($this->filterBuilder->create());
        }

        if (isset($value['to'])) {
            $this->filterBuilder
                ->setField($this->getName())
                ->setValue($value['to'])
                ->setConditionType('to');

            $dataProvider->addFilter($this->filterBuilder->create());
        }

        return $this;
    }
}
