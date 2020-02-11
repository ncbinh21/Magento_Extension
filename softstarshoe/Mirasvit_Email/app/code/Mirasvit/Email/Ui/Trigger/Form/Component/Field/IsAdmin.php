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
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Ui\Trigger\Form\Component\Field;


use Magento\Ui\Component\Form\Field;
use Mirasvit\Email\Api\Data\TriggerInterface;

class IsAdmin extends Field
{
     /**
      * Set "is_admin" field's value to 1 if Administrator trigger is created.
      *
      * {@inheritDoc}
      */
    public function prepare()
    {
        if ($this->getContext()->getRequestParam(TriggerInterface::IS_ADMIN, false)) {
            $this->setData('config', array_merge($this->getData('config'), ['value' => 1]));
        }

        parent::prepare();
    }
}
