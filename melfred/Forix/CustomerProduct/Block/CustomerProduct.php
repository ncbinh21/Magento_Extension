<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 11/07/2018
 * Time: 17:32
 */

namespace Forix\CustomerProduct\Block;
class CustomerProduct extends \Magento\Customer\Block\Account\Dashboard
{

    /**
     * @var \Forix\CustomerProduct\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * CustomerProduct constructor.
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Forix\CustomerProduct\Model\ResourceModel\Order\Item\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Forix\CustomerProduct\Model\ResourceModel\Order\Item\CollectionFactory $collectionFactory,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = [])
    {
        $this->categoryRepository = $categoryRepository;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context, $customerSession, $subscriberFactory, $customerRepository, $customerAccountManagement, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($collection = $this->getProductCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'customer.product.pager'
            )->setCollection(
                $collection
            );
            $this->setChild('pager', $pager);
        }
        return $this;
    }
    /**
     * @return \Forix\CustomerProduct\Model\ResourceModel\Customer\Product\Collection
     */
    public function getProductCollection(){
        $paramSortBy = $this->getRequest()->getParam('sortby');
        $paramCategory = $this->getRequest()->getParam('category');
        $collection = $this->collectionFactory->create();
        $collection->addCustomerToFilter($this->getCustomer()->getId());
        switch ($paramSortBy)
        {
            case "date":
                $collection->setOrder(
                    'created_order',
                    'desc'
                );
                break;
            case "category":
                $collection->setOrder(
                    'category_flat.name',
                    'asc'
                );
                break;
            case "sku":
                $collection->setOrder(
                    'sku',
                    'asc'
                );
                break;
            case "rig":
                $collection->setOrder(
                    'rig_model',
                    'asc'
                );
                break;
        }
        switch ($paramCategory)
        {
            case "all":
                $collection->getAllCategory();
                break;
            case 0:
                $collection->getAllCategory();
                break;
            case $paramCategory:
                $collection->filterByCategory($paramCategory);
                break;
            default:
                $collection->getAllCategory();
                break;
        }

        //sort by id
        $collection->setOrder(
            'created_order',
            'desc'
        );
        return $collection;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    /**
     * @param $product
     * @return string
     */
    public function getImageOriginal($product)
    {
        $store = $this->storeManager->getStore();
        return $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
    }

    /**
     * @param $product
     * @return array
     */
    public function getItemOptions($product)
    {
        $result = [];
        $options = $product->getProductOptions();


        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $this->sortProductOptions($result);
    }

    /**
     * @param $result
     * @return mixed
     */
    protected function sortProductOptions($result)
    {
        $rigModel = [];
        if (isset($result) && !empty($result)){
            foreach ($result as $key => $value) {
                if ($value['label'] == __("Your Rig Model")){
                    $rigModel = $result[$key];
                    unset($result[$key]);
                    break;
                }
            }
        }

        if($rigModel) {
            array_unshift($result, $rigModel);
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getSortByOptions() {
        $sortby = $this->getRequest()->getParam('sortby');
        return [
            'date' => [
                'label' => 'Newest',
                'selected' => !$sortby || 'date' == $sortby ? 'selected' : ''
            ],
            'category' => [
                'label' => 'Category',
                'selected' => 'category' == $sortby ? 'selected' : ''
            ],
            'sku' => [
                'label' => 'Part #',
                'selected' => 'sku' == $sortby ? 'selected' : ''
            ],
            'rig' => [
                'label' => 'Rig Model',
                'selected' => 'rig' == $sortby ? 'selected' : ''
            ],
        ];
    }

    /**
     * @return array
     */
    public function getFilterCategoryOptions() {
        $param = $this->getRequest()->getParam('category');
        $collections = $this->collectionFactory->create();
        $collections->addCustomerToFilter($this->getCustomer()->getId());
        $listCategory['all'] = [
            'label' => 'All Category',
            'selected' => !$param || 'all' == $param ? 'selected' : ''
        ];
        $temp = [];
        $collections->getListCategoryId();
        if($collections->count() > 0) {
            foreach ($collections as $item)
            {
                $listCateId = explode(',',$item->getCateId());
                foreach ($listCateId as $cateId) {
                    if(!in_array($cateId, $temp)) {
                        array_push($temp, $cateId);
                    }
                }
            }
        }
        foreach ($temp as $item) {
            $category =  $this->categoryRepository->get($item);
            $listCategory[$item] = [
                'label' => $category->getName(),
                'selected' => $item == $param ? 'selected' : ''
            ];
        }
        return $listCategory;
    }

    /**
     * @param $product
     * @return string
     */
    public function getUrlImageProduct($product)
    {
        if($product){
            return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
        }
    }
}