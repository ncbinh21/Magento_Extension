<?php


namespace Forix\ProductWizard\Controller\Adminhtml\GroupItem;

class Edit extends \Forix\ProductWizard\Controller\Adminhtml\GroupItem
{

    protected $resultPageFactory;
    
    protected $_groupItemFactory;
    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Forix\ProductWizard\Model\GroupItemFactory $groupItemFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Forix\ProductWizard\Model\GroupItemFactory $groupItemFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_groupItemFactory = $groupItemFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('group_item_id');
        $model = $this->_groupItemFactory->create();
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Group Item no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('forix_productwizard_group_item', $model);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Group Item') : __('New Group Item'),
            $id ? __('Edit Group Item') : __('New Group Item')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Group Items'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Group Item'));
        return $resultPage;
    }
}
