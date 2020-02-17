<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name:
 * Date: 06/09/2018
 * Time: 11:25
 */

namespace Forix\ProductWizard\Controller\Adminhtml\GroupItem;

class Grid extends \Forix\ProductWizard\Controller\Adminhtml\GroupItem
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    protected $_groupItemFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Forix\ProductWizard\Model\GroupItemFactory $groupItemFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Registry $coreRegistry)
    {
        parent::__construct($context, $coreRegistry);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_groupItemFactory = $groupItemFactory;
    }


    protected function _initGroupItem()
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
        return $model;
    }

    /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $groupItem = $this->_initGroupItem();
        if (!$groupItem) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('forix_productwizard/*/', ['_current' => true, 'id' => null]);
        }
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                \Forix\ProductWizard\Block\Adminhtml\Group\Item\Product::class,
                'group.item.product.grid'
            )->toHtml()
        );
    }
}
