<?php

namespace Forix\CustomerOrder\Block\Orders;

use \Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\OrderRepository;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;


class Manage extends \Magento\Framework\View\Element\Template
{

    protected $_orderCollectionFactory;
    protected $_customerSession;
    protected $_orderConfig;
    protected $orders;
    private $orderCollectionFactory;
    protected $shippingConfig;
    protected $messageManager;
    protected $addressRenderer;
    protected $_dataHelper;
    protected $orderRepository;
    protected $countryFactory;
    protected $orderFactory;

    public function __construct(
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Forix\CustomerOrder\Helper\Data $dataHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->countryFactory = $countryFactory;
        $this->orderRepository = $orderRepository;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
        $this->addressRenderer = $addressRenderer;
        $this->shippingConfig = $shippingConfig;
        $this->messageManager = $messageManager;
        $this->_dataHelper = $dataHelper;
        $this->orderFactory = $orderFactory;

        parent::__construct($context, $data);
    }

    /**
     * @param $countryCode
     * @return string
     */
    public function getCountryNameFromCode($countryCode) {
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Orders'));
    }

    /**
     * @return CollectionFactoryInterface|mixed
     */
    private function getOrderCollectionFactory()
    {
        if ($this->orderCollectionFactory === null) {
            $this->orderCollectionFactory = ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        }
        return $this->orderCollectionFactory;
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {
        if (!$this->_dataHelper->getDistributorPostcode()) {
            return false;
        }
        $distributorPostcode = $this->_dataHelper->getDistributorPostcode();
        if (!$this->orders && $distributorPostcode) {
            $this->orders = $this->getOrderCollectionFactory()->create()
                ->join(['soa' => 'sales_order_address'], 'main_table.entity_id = soa.parent_id', [])
                ->addFieldToSelect('*')
                ->addFieldToFilter('soa.postcode', ['in' => $distributorPostcode])
                ->addFieldToFilter('soa.address_type', ['eq' => 'billing'])
                ->addFieldToFilter('main_table.distributor_fulfilled', ['eq' => 1])
                ->addFieldToFilter(
                    'main_table.status',
                    ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
                );

            $this->updateCondition();
        }
        return $this->orders;
    }

    /**
     * @return $this
     */
    public function updateCondition()
    {

        $params["sortby"] = $this->getRequest()->getParam('sortby');
        switch ($params["sortby"]) {
            case "name":

                $this->orders->addAddressFields();
                $this->orders->setOrder(
                    'shipping_o_a.' . 'firstname',
                    'asc'
                );
                break;
            case "total":
                $this->orders->setOrder(
                    'main_table.' . 'grand_total',
                    'desc'
                );
                break;
            case "status":
                $this->orders->setOrder(
                    'main_table.' . 'status',
                    'desc'
                );
                break;
            default:
        }

        $params["status"] = $this->getRequest()->getParam('status');
        switch ($params["status"]) {
            case "all":
                break;
            case "pending":

                $this->orders->addFieldToFilter(
                    'main_table.status',
                    ['=' => 'pending']
                );
                break;
            case "complete":
                $this->orders->addFieldToFilter(
                    'main_table.status',
                    ['=' => 'complete']
                );
                break;
//            case "processing":
            default:
                $this->orders->addFieldToFilter(
                    'main_table.status',
                    ['=' => 'processing']
                );
                break;

        }

        //sort by id
        $this->orders->setOrder(
            'main_table.' . 'entity_id',
            'desc'
        );
        return $this;
    }

    /**
     * @return array
     */
    public function getSortByOptions()
    {
        $sortby = $this->getRequest()->getParam('sortby');
        return [
            'date' => [
                'label' => 'Date Ordered',
                'selected' => !$sortby || 'date' == $sortby ? 'selected' : ''
            ],
            'status' => [
                'label' => 'Status',
                'selected' => 'status' == $sortby ? 'selected' : ''
            ],
            'id' => [
                'label' => 'Order#',
                'selected' => 'id' == $sortby ? 'selected' : ''
            ],
            'name' => [
                'label' => 'Ship Name',
                'selected' => 'name' == $sortby ? 'selected' : ''
            ],
            'total' => [
                'label' => 'Order Total',
                'selected' => 'total' == $sortby ? 'selected' : ''
            ],

        ];
    }

    /**
     * @return array
     */
    public function getFilterStatusOptions()
    {
        $param = $this->getRequest()->getParam('status');
        return [
            'all' => [
                'label' => 'All Orders',
                'selected' => 'all' == $param ? 'selected' : ''
            ],
            'pending' => [
                'label' => 'Pending',
                'selected' => 'pending' == $param ? 'selected' : ''
            ],
            'processing' => [
                'label' => 'Processing',
                'selected' => !$param || 'processing' == $param ? 'selected' : ''
            ],
            'complete' => [
                'label' => 'Complete',
                'selected' => 'complete' == $param ? 'selected' : ''
            ],
        ];
    }


    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($collection = $this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'sales.order.history.pager'
            )->setCollection(
                $collection
            );
            $this->setChild('pager', $pager);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getTrackUrl($order)
    {
        return $this->getUrl('sales/order/track', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    /**
     * @return \Magento\Sales\Model\Order\Address\Renderer
     */
    public function getAddressRenderer()
    {
        return $this->addressRenderer;
    }

    /**
     * @return array
     */
    public function getCarrier()
    {
        $carriers = [];
        $carrierInstances = $this->shippingConfig->getAllCarriers();
        $carriers['custom'] = __('Custom');
        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }
        return $carriers;
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
     * @param $state
     * @param $orderId
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStatusOrderEnable($state, $orderId)
    {
        $arrayStatus = array();
        $order = $this->orderFactory->create()->load($orderId);
        $shipments = $order->getShipmentsCollection()->getItems();
        if(count($shipments) == 1 && $order->canShip()) {
            return $arrayStatus;
        }

        if(count($shipments) > 1  && $order->canShip()) {
            return $arrayStatus;
        }
        switch ($state)
        {
            case 'new':
                array_push($arrayStatus, 'processing');
                array_push($arrayStatus, 'complete');
                return $arrayStatus;
            case 'processing':
                array_push($arrayStatus, 'complete');
                return $arrayStatus;
            default:
                return $arrayStatus;
        }
    }

    /**
     * @return mixed
     */
    public function getActiveOrder()
    {
        if(isset($_COOKIE['customer_order_id'])) {
            return $_COOKIE['customer_order_id'];
        }
    }

    /**
     * remove cookie
     */
    public function removeCookieOrderId()
    {
        setcookie('customer_order_id', null, -1, '/');
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
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'catalog/product/placeholder/'. $this->getConfig('catalog/placeholder/thumbnail_placeholder');
    }

    /**
     * @param $config_path
     * @return mixed
     */
    public function getConfig($config_path)
    {
        return $this->_storeManager->getStore()->getConfig($config_path);
    }
}
