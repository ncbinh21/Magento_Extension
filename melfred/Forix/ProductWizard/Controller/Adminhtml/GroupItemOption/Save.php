<?php


namespace Forix\ProductWizard\Controller\Adminhtml\GroupItemOption;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;
    protected $_groupItemOptionFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Forix\ProductWizard\Model\GroupItemOptionFactory $groupItemOptionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_groupItemOptionFactory = $groupItemOptionFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('group_item_option_id');
        
            $model = $this->_groupItemOptionFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Group Item Option no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);
        
            try {
                if(!$model->getOptionId()){
                    $model->setOptionId(null);
                }
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Group Item Option.'));
                $this->dataPersistor->clear('forix_productwizard_group_item_option');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['group_item_option_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Group Item Option.'));
            }
        
            $this->dataPersistor->set('forix_productwizard_group_item_option', $data);
            return $resultRedirect->setPath('*/*/edit', ['group_item_option_id' => $this->getRequest()->getParam('group_item_option_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
