<?php


namespace Forix\ProductWizard\Controller\Adminhtml\GroupItemOption;

class Edit extends \Forix\ProductWizard\Controller\Adminhtml\GroupItemOption
{

    protected $resultPageFactory;
    protected $_groupItemOptionFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Forix\ProductWizard\Model\GroupItemOptionFactory $groupItemOptionFactory, 
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_groupItemOptionFactory = $groupItemOptionFactory;
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
        $id = $this->getRequest()->getParam('group_item_option_id');
        /**
         * @var $model \Forix\ProductWizard\Model\GroupItemOption
         */
        $model = $this->_groupItemOptionFactory->create();
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Group Item Option no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('forix_productwizard_group_item_option', $model);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Group Item Option') : __('New Group Item Option'),
            $id ? __('Edit Group Item Option') : __('New Group Item Option')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Group Item Options'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Group Item Option'));
        return $resultPage;
    }
}
