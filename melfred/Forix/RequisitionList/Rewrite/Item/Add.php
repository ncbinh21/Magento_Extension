<?php

namespace Forix\RequisitionList\Rewrite\Item;

use Magento\Framework\Controller\ResultFactory;
use Forix\RequisitionList\Model\ResourceModel\Quote\Item;
use Magento\Catalog\Model\ProductRepository;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;


class Add extends \Magento\RequisitionList\Controller\Item\Add
{
	/**
	 * @var \Magento\RequisitionList\Model\Action\RequestValidator
	 */
	private $requestValidator;

	/**
	 * @var \Magento\RequisitionList\Model\RequisitionListItem\SaveHandler
	 */
	private $requisitionListItemSaveHandler;

	/**
	 * @var \Magento\RequisitionList\Model\RequisitionListProduct
	 */
	private $requisitionListProduct;

	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	private $logger;

	/**
	 * @var \Magento\Catalog\Api\Data\ProductInterface
	 */
	private $product;

	/**
	 * @var \Magento\RequisitionList\Model\RequisitionListItem\Locator
	 */
	private $requisitionListItemLocator;

	protected $_quoteCollection;

	protected $_productRepository;

	protected $_jsonHelper;

	protected $_collectionFactory;


	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\RequisitionList\Model\Action\RequestValidator $requestValidator
	 * @param \Magento\RequisitionList\Model\RequisitionListItem\SaveHandler $requisitionListItemSaveHandler
	 * @param \Magento\RequisitionList\Model\RequisitionListProduct $requisitionListProduct
	 * @param \Psr\Log\LoggerInterface $logger
	 * @param \Magento\RequisitionList\Model\RequisitionListItem\Locator $requisitionListItemLocator
	 */

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\RequisitionList\Model\Action\RequestValidator $requestValidator,
		\Magento\RequisitionList\Model\RequisitionListItem\SaveHandler $requisitionListItemSaveHandler,
		\Magento\RequisitionList\Model\RequisitionListProduct $requisitionListProduct,
		\Psr\Log\LoggerInterface $logger,
		\Magento\RequisitionList\Model\RequisitionListItem\Locator $requisitionListItemLocator,
		Item $item,
		ProductRepository $productRepository,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		CollectionFactory $collectionFactory
	) {
		parent::__construct($context, $requestValidator, $requisitionListItemSaveHandler, $requisitionListProduct,
			$logger, $requisitionListItemLocator);
		$this->requestValidator = $requestValidator;
		$this->requisitionListItemSaveHandler = $requisitionListItemSaveHandler;
		$this->requisitionListProduct = $requisitionListProduct;
		$this->logger = $logger;
		$this->requisitionListItemLocator = $requisitionListItemLocator;
		$this->_quoteCollection = $item;
		$this->_productRepository = $productRepository;
		$this->_jsonHelper = $jsonHelper;
		$this->_collectionFactory = $collectionFactory;
	}

	/**
	 * @inheritdoc
	 */
	public function execute()
	{
		$params = $this->_request->getParams();
		$saleItemId = "";
		if (isset($params["product_data"])) {
			$productData = $params["product_data"];
			$productData = $this->_jsonHelper->jsonDecode($productData);
			if (isset($productData["sales_item_id"])) {
				$saleItemId = $productData["sales_item_id"];
			}
			if (!isset($productData["options"])) {
				$this->addListFromCart();
				$collection = $this->_collectionFactory->create();
				$collection->addFieldToFilter('item_id', ['eq'=>(INT)$saleItemId]);
				if ($collection->getSize()) {
					$data    = $collection->getFirstItem();
					$options = $data->getData("product_options")["info_buyRequest"];
					unset($options["uenc"]);
					if (isset($options["rig_model"])) {
						$val = $options["rig_model"];
						$options["rig"] = $val;
						$options["form_key"] = $this->getRequest()->getParam("form_key");
						unset($options["rig_model"]);
					}
					unset($productData["sales_item_id"]);
					$productData["options"] = http_build_query($options);
					$productData = $this->_jsonHelper->jsonEncode($productData);
					$params["product_data"] = $productData;
					$this->getRequest()->setParams($params);
				}
			}
		}

		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$redirect = $this->preExecute($resultRedirect);
		if ($redirect) {
			return $redirect;
		}

		$itemId = (int)$this->getRequest()->getParam('item_id');
		$listId = $this->findRequisitionListByItemId($itemId);

		try {
			$options = [];
			$productData = $this->requisitionListProduct->prepareProductData(
				$this->getRequest()->getParam('product_data')
			);
			if (is_array($productData->getOptions())) {
				$options = $productData->getOptions();
			}

			$redirect = $this->checkConfiguration($resultRedirect, $options, $itemId, $listId);
			if ($redirect) {
				return $redirect;
			}

			$message = $this->requisitionListItemSaveHandler->saveItem($productData, $options, $itemId, $listId);
			$this->messageManager->addSuccess($message);
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
			$this->messageManager->addError($e->getMessage());
		} catch (\Exception $e) {
			if ($itemId) {
				$this->messageManager->addError(__('We can\'t update your requisition list right now.'));
			} else {
				$this->messageManager->addErrorMessage(
					__('We can\'t add the item to the Requisition List right now: %1.', $e->getMessage())
				);
			}
			$this->logger->critical($e);
		}

		if (!$itemId) {
			return $resultRedirect->setRefererUrl();
		}

		return $resultRedirect->setPath(
			'requisition_list/requisition/view',
			['requisition_id' => $listId]
		);


	}

	/**
	 * Check is product configuration correct and requisition list id exists.
	 *
	 * @param \Magento\Framework\Controller\ResultInterface $resultRedirect
	 * @param array $options
	 * @param int $itemId
	 * @param int $listId
	 * @return \Magento\Framework\Controller\ResultInterface|null
	 */
	private function checkConfiguration(
		\Magento\Framework\Controller\ResultInterface $resultRedirect,
		array $options,
		$itemId,
		$listId
	) {
		if (!$listId) {
			$this->messageManager->addError(__('We can\'t specify a requisition list.'));
			$resultRedirect->setPath('requisition_list/requisition/index');
			return $resultRedirect;
		}

		if (!$itemId && empty($options)
			&& $this->requisitionListProduct->isProductShouldBeConfigured($this->getProduct())) {
			$this->messageManager->addErrorMessage(__('You must choose options for your item.'));
			$resultRedirect->setUrl($this->getProductConfigureUrl());
			return $resultRedirect;
		}

		return null;
	}

	/**
	 * Check is add to requisition list action allowed for the current user and product exists.
	 *
	 * @param \Magento\Framework\Controller\ResultInterface $resultRedirect
	 * @return \Magento\Framework\Controller\ResultInterface|null
	 */
	private function preExecute(\Magento\Framework\Controller\ResultInterface $resultRedirect)
	{
		$result = $this->requestValidator->getResult($this->getRequest());
		if ($result) {
			return $result;
		}

		if (!$this->getProduct()) {
			$this->messageManager->addError(__('We can\'t specify a product.'));
			$resultRedirect->setPath('requisition_list/requisition/index');
			return $resultRedirect;
		}
		return null;
	}

	/**
	 * Get product specified by product data.
	 *
	 * @return \Magento\Catalog\Api\Data\ProductInterface|bool
	 */
	private function getProduct()
	{

		if ($this->product === null) {
			$productData = $this->requisitionListProduct->prepareProductData(
				$this->getRequest()->getParam('product_data')
			);
			$this->product = $this->requisitionListProduct->getProduct($productData->getSku());
		}
		return $this->product;
	}

	/**
	 * Prepare product configure url.
	 *
	 * @return string
	 */
	private function getProductConfigureUrl()
	{
		return $this->getProduct()->getUrlModel()->getUrl(
			$this->getProduct(),
			['_fragment' => 'requisition_configure']
		);
	}

	/**
	 * Find requisition list by item id.
	 *
	 * @param int $itemId
	 * @return int|null
	 */
	private function findRequisitionListByItemId($itemId)
	{
		$listId = $this->getRequest()->getParam('list_id');
		if (!$listId && $itemId) {
			$item = $this->requisitionListItemLocator->getItem($itemId);
			$listId = $item->getRequisitionListId();
		}

		return $listId;
	}


	protected function addListFromCart()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$cart = $objectManager->get('\Magento\Checkout\Model\Cart');
		$quote = $cart->getQuote();
		$quoteId = $quote->getId();
		$params = $this->_request->getParams();
		$arr = $this->_jsonHelper->jsonDecode($params["product_data"]);
		$sku = $arr["sku"];
		$result = $this->_quoteCollection->getItemBySku($sku, $quoteId);
		if (!empty($result)) {
			$value  = $this->_jsonHelper->jsonDecode($result["value"]);
			$sku = "";
			if (isset($value["product"])) {
				$p = $this->_productRepository->getById($value["product"]);
				$sku = $p->getSku();
			}
			unset($value["uenc"]);
			$query_array = array();
			foreach( $value as $key => $key_value ){
				if (!is_array($key_value)) {
					$query_array[] = urlencode( $key ) . '=' . urlencode( $key_value );
				} else {

					if ($key == 'super_attribute') {
						if (isset($value["rig_model"])) {
							$rig = $value["rig_model"];
							$rigAtt = array_keys($rig)[0];
							$value  = $rig[$rigAtt];
							$query_array[] = urlencode('super_attribute').'%5B'.$rigAtt.'%5D'.'='. urlencode( $value );
						}
						foreach ($key_value as $k => $attr) {
							$query_array[] = urlencode('super_attribute').'%5B'.$k.'%5D'.'='. urlencode( $attr );
						}
					}
					if ($key == "options") {
						foreach ($key_value as $k2=>$op) {
							$query_array[] = urlencode('options').'%5B'.$k2.'%5D'.'='. urlencode( $op );

						}
					}
				}
			}
			$options =  implode("&", $query_array);
			$paramsProductData = $this->_jsonHelper->jsonDecode($params["product_data"]);
			$paramsProductData["sku"]     = $sku;
			$paramsProductData["options"] = $options;
			$paramsProductData = $this->_jsonHelper->jsonEncode($paramsProductData);
			$this->_request->setParams(['product_data' => $paramsProductData]);
		}

	}

}