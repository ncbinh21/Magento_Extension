<?php

namespace Forix\Custom\Rewrite\Amasty\Storelocator\Adminhtml\Location;

use Amasty\Storelocator\Model\LocationFactory;

class MassAction extends \Amasty\Storelocator\Controller\Adminhtml\Location\MassAction
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

    public function execute()
    {
        $ids = $this->getRequest()->getParam('location');
        $action = $this->getRequest()->getParam('action');
        if ($ids && in_array($action, ['activate', 'inactivate', 'delete'])) {
            try {
                /**
                 * @var $collection \Amasty\Storelocator\Model\ResourceModel\Location\Collection
                 */
                $collection = $this->_location->create()->getCollection();

                $collection->addFieldToFilter('id', array('in'=>$ids));
                $collection->walk($action);

                if($action == 'delete') {
                    $query = $this->resource->getConnection();
                    $query->delete('forix_distributor_zipcode',  ['distributor_id in (?)' => $ids]);
                }

                $this->messageManager->addSuccess(__('You deleted the location(s).'));
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete location(s) right now. Please review the log and try again.').$e->getMessage()
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('*/*/');
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a location(s) to delete.'));
        $this->_redirect('*/*/');
    }
}