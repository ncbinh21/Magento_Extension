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


namespace Mirasvit\Credit\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Options\Type;

use Mirasvit\Credit\Block\Adminhtml\Catalog\Product\Composite\Fieldset\CreditOptions;
use Mirasvit\Credit\Ui\DataProvider\Product\Form\Modifier\Composite;

class Simple extends CreditOptions
{
    /**
     * {@inherite}
     */
    protected $_template = 'product/composite/fieldset/options/type/simple.phtml';

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Composite::PRICE_TYPE_SINGLE;
    }
}