<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Controller\Adminhtml\Template;

use Yosto\InstagramConnect\Controller\Adminhtml\Template;

/**
 * Class NewAction
 * @package Yosto\InstagramConnect\Controller\Adminhtml\Template
 */
class NewAction extends Template
{
    /**
     * Execute when create new Template
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}