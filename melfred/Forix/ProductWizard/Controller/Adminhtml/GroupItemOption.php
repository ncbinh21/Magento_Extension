<?php


namespace Forix\ProductWizard\Controller\Adminhtml;

abstract class GroupItemOption extends \Magento\Backend\App\Action
{

    protected $_coreRegistry;
    const ADMIN_RESOURCE = 'Forix_ProductWizard::Group_Item_Option';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Forix'), __('Forix'))
            ->addBreadcrumb(__('Group Item Option'), __('Group Item Option'));
        return $resultPage;
    }
}
