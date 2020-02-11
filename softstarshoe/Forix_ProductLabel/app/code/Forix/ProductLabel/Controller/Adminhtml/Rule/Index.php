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
namespace Forix\ProductLabel\Controller\Adminhtml\Rule;

use Forix\ProductLabel\Controller\Adminhtml\AbstractRule;

/**
 * Class Index
 * @package Forix\ProductLabel\Controller\Adminhtml\Rule
 */
class Index extends AbstractRule
{

    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Badge Rules'));
        $this->_view->renderLayout();
    }
}
