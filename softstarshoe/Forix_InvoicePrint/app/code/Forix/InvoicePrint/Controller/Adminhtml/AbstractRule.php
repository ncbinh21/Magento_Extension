<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Start Shoes
 * Date: 01/03/2018
 * Time: 11:49
 */
namespace Forix\InvoicePrint\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Forix\InvoicePrint\Model\RuleFactory;

/**
 * Class AbstractRule
 * @package Forix\InvoicePrint\Controller\Adminhtml
 */
abstract class AbstractRule extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Forix_InvoicePrint::rules';

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
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Forix_InvoicePrint::invoiceprint_rules');
    }

    /**
     * Initiate rule
     *
     * @return \Forix\InvoicePrint\Model\Rule
     */
    protected function _initRule()
    {
        $rule = $this->_ruleFactory->create();
        $this->_coreRegistry->register(
            'current_invoiceprint_rule',
            $rule
        );
        $id = (int)$this->getRequest()->getParam('id');

        if (!$id && $this->getRequest()->getParam('rule_id')) {
            $id = (int)$this->getRequest()->getParam('rule_id');
        }

        if ($id) {
            $this->_coreRegistry->registry('current_invoiceprint_rule')->load($id);
        }
        return $this->_coreRegistry->registry('current_invoiceprint_rule');
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
            'Forix_InvoicePrint::invoiceprint_rules'
        )->_addBreadcrumb(
            __('Manage Break Invoice Item Rules'),
            __('Manage Break Invoice Item Rules')
        );
        return $this;
    }
}
