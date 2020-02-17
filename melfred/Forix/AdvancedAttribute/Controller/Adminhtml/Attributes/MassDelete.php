<?php
namespace Forix\AdvancedAttribute\Controller\Adminhtml\Attributes;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
class MassDelete extends Action
{

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(\Magento\Backend\App\Action\Context $context)
    {
        parent::__construct($context);
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('banner_option');
        $_resourceLink = $this->getRequest()->getParam('rs');
        $_resourceLink = explode('|',$_resourceLink);

        $_attributeId  = $_resourceLink[1];
        $_attributeCode = $_resourceLink[0];//echo $_attributeCode;die;

        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select a banner(s).'));
        } else {
            foreach ($ids as $id) {
                $model = $this->_objectManager->create('Forix\AdvancedAttribute\Model\Option')->load($id);
                $model->delete();
            }
            try {
                $this->messageManager->addSuccess(__('You deleted %1 record(s).', count($ids)));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while mass-deleting banners. Please review the action log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('attribute_banners/*/manage/attrid/'.$_attributeId.'/attrcode/'.$_attributeCode);
            }
        }

        $this->_redirect('attribute_banners/*/manage/attrid/'.$_attributeId.'/attrcode/'.$_attributeCode);
    }
}