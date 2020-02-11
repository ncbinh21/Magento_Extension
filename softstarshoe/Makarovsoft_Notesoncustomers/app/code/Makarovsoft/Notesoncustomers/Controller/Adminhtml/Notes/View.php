<?php
/**
 * Copyright © 2015 Makarovsoft. All rights reserved.
 */

namespace Makarovsoft\Notesoncustomers\Controller\Adminhtml\Notes;

class View extends \Makarovsoft\Notesoncustomers\Controller\Adminhtml\Notes
{

    /**
     * Customer orders grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
