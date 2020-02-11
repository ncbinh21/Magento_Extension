<?php

namespace Forix\Custom\Controller\Category;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Product extends \Magento\Framework\App\Action\Action
{
    /**
     * @var
     */
    protected $storeManager;

    /**
     * @var
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Product constructor.
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param Context $context
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->resultLayoutFactory  = $resultLayoutFactory;
        $this->_storeManager        = $storeManager;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new \Exception('Wrong request.');
        }
        $resultLayout = $this->resultLayoutFactory->create();
        $productLoad = $resultLayout->getLayout()->getBlock('catalog-product-homepage');

        $categoryId = $this->getRequest()->getParam('category_id');
        $store = $this->_storeManager->getStore();

        $category = $this->categoryFactory->create();
        $products = $category->load($categoryId)->getProductCollection();
        $products->addAttributeToSelect('*')->addAttributeToFilter('sss_most_loved_styles',array('eq' => 1))->setOrder('position','ASC')->setPageSize(12);

        $data['html'] = $productLoad->setCollection($products)->toHtml();
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($data)
        );
    }

    public function getLayout() {
        return $this->_layout;
    }
}