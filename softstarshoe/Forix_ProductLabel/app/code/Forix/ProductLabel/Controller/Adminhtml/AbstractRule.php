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

namespace Forix\ProductLabel\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Forix\ProductLabel\Model\RuleFactory;

/**
 * Class AbstractRule
 * @package Forix\ProductLabel\Controller\Adminhtml
 */
abstract class AbstractRule extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Forix_ProductLabel::label';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * Date filter instance
     *
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * AbstractRule constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     * @param RuleFactory $ruleFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Date $dateFilter,
        RuleFactory $ruleFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_dateFilter = $dateFilter;
        $this->_ruleFactory = $ruleFactory;
    }

    /**
     * Init action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Forix_ProductLabel::rule_label'
        )->_addBreadcrumb(
            __('Manage Rule'),
            __('Manage Rule')
        );
        return $this;
    }
}
