<?php
/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */

/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */
namespace Forix\InvoicePrint\Controller\Adminhtml\Rule;

use Forix\InvoicePrint\Controller\Adminhtml\AbstractRule;

/**
 * Class NewAction
 * @package Forix\InvoicePrint\Controller\Adminhtml\Rule
 */
class NewAction extends AbstractRule
{

    /**
     * New rule action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
