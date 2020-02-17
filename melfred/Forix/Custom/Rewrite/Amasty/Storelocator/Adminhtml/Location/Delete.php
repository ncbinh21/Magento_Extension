<?php

namespace Forix\Custom\Rewrite\Amasty\Storelocator\Adminhtml\Location;

use Amasty\Storelocator\Model\LocationFactory;

class Delete extends \Amasty\Storelocator\Controller\Adminhtml\Location\Delete
{
    protected $resource;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        LocationFactory $location,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $filesystem, $fileUploaderFactory, $serializer, $ioFile, $location);
    }
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->_location->create();
                $model->load($id);
                $model->delete();

                $query = $this->resource->getConnection();
                $query->delete('forix_distributor_zipcode', 'distributor_id = '.$id.'');

                $this->messageManager->addSuccess(__('You deleted the item.'));
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        $this->_redirect('*/*/');
    }
}