<?php


namespace Forix\ProductWizard\Controller\Adminhtml\GroupItem;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;
    protected $_groupItemFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Forix\ProductWizard\Model\GroupItemFactory $groupItemFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_groupItemFactory = $groupItemFactory;
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
            $id = $this->getRequest()->getParam('group_item_id');
        
            $model = $this->_groupItemFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Group Item no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        
            $model->setData($data);
        
            try {
                if(!$model->getAttributeCode()){
                    $model->setAttributeCode(null);
                }
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Group Item.'));
                $this->dataPersistor->clear('forix_productwizard_group_item');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['group_item_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            }catch (NoSuchEntityException $e){
                $this->messageManager->addExceptionMessage($e, __('Attribute Code Does not exists.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Group Item.'));
            }
        
            $this->dataPersistor->set('forix_productwizard_group_item', $data);
            return $resultRedirect->setPath('*/*/edit', ['group_item_id' => $this->getRequest()->getParam('group_item_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
