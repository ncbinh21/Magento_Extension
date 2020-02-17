<?php
namespace Forix\AdvancedAttribute\Controller\Adminhtml\Attributes;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;

class Edit extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;
    protected $_resultPageFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Registry $coreRegistry
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }

    public function execute()
    {

        $id = $this->getRequest()->getParam('id');
        
        $model = $this->_objectManager->create('Forix\AdvancedAttribute\Model\Option');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This banner option no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                
                return $resultRedirect->setPath('*/*/manage', ['_current' => true]);
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('current_banner_option', $model);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Advanced Option'));
        
        return $resultPage;
    }

}