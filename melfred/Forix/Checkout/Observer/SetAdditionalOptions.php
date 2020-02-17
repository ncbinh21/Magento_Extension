<?php

namespace Forix\Checkout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\ObjectManager;
use Forix\Shopby\Model\ResourceModel\ResourceHelperFactory;
use Magento\Framework\Registry;

class SetAdditionalOptions implements ObserverInterface
{
	/**
	 * @var Json
	 */
	protected $serializer;
	/**
	 * @var RequestInterface
	 */
	protected $_request;

	protected $resourceHelperFactory;

	protected $_registry;

	public function __construct(
		RequestInterface $request,
        ResourceHelperFactory $resourceHelperFactory,
        Registry $registry,
        Json $serializer = null
	) {
		$this->_request = $request;
		$this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
		$this->resourceHelperFactory = $resourceHelperFactory;
		$this->_registry      = $registry;
	}

	/**
	 * @param \Magento\Framework\Event\Observer $observer
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$fullAction = ['checkout_cart_add', 'checkout_cart_updateItemOptions','checkout_cart_configureFailed','checkout_cart_updateFailedItemOptions'];
		if (in_array($this->_request->getFullActionName(), $fullAction)) {
			$params = $this->_request->getParams();
			if (isset($params["rig_model"]) ) {
				$rigModel = $params["rig_model"];
				$attId   = array_keys($rigModel);
				$rigId   = $params["rig_model"][$attId[0]];
				if ($rigId!=0) {
					$rigData = $this->resourceHelperFactory->create()->getOptionById($rigId);
					$additionalOptions = [];
					$additionalOptions[] = array(
						'label' => __("Your Rig Model"),
						'value' => $rigData["value"],
						'option_id'=>$rigData["option_id"],
						'option_value'=>$rigId
					);
					$data = $this->serializer->serialize($additionalOptions);
					$observer->getProduct()->addCustomOption('additional_options', $data);
				}
			}
		} else if ($this->_request->getFullActionName() == "sales_order_reorder") {
			$p = $observer->getProduct();
			$order = $this->_registry->registry('reorder');
			$data = $order->getItems();
            /**
             * @var $_item \Magento\Sales\Api\Data\OrderItemInterface
             */
			foreach ($data as $_item) {
				if ($_item->getProductId() == $p->getId()) {
					$productOptions = $_item->getProductOptions();
					if (isset($productOptions["additional_options"])) {
						$additionalOptions = $productOptions["additional_options"];
						$additional = $this->serializer->serialize($additionalOptions);
						$observer->getProduct()->addCustomOption('additional_options', $additional);
					}
					break;
				}
			}
		}
	}
}