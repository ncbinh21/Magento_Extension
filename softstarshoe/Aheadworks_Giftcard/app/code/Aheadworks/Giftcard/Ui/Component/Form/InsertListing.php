<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\Component\Form;

use Magento\Ui\Component\Container;

/**
 * Class InsertListing
 *
 * @package Aheadworks\Giftcard\Ui\Component\Form
 */
class InsertListing extends Container
{
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $id = $this->getContext()->getRequestParam(
            $this->getContext()->getDataProvider()->getRequestFieldName(),
            'new'
        );
        $config = $this->getData('config');
        $config['params'][$config['addParamToFilter']] = $id;
        $this->setData('config', $config);

        parent::prepare();
    }
}
