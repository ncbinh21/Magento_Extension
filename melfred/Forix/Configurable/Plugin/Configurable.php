<?php

namespace Forix\Configurable\Plugin;

use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;
use Forix\Configurable\Helper\Data;
use Forix\Base\Helper\Data as BaseHelper;
use Forix\Quote\Model\itemOption;
use Magento\RequisitionList\Model\RequisitionListItem;
use Forix\Shopby\Model\ResourceModel\ResourceHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Configurable
{
    protected $jsonEncoder;
    protected $jsonDecoder;
    protected $childItems;
    protected $helper;
    protected $baseHelper;
    protected $_usedProducts;
    protected $_radioSwatchHelper;
    protected $itemOption;
    protected $requisitionListItem;
	protected $resourceHelper;
	protected $scopeConfigInterface;
	protected $_registry;

	/**
	 * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
	 */
	protected $_attributeOptionCollection;
	protected $_categoryFactory;
    protected $productRepository;
    protected $dataHelper;
    protected $dataConfig;
    protected $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        Data $helper,
        \Forix\Configurable\Helper\RadioSwatchHelper $radioSwatchHelper,
        BaseHelper $basehelper,
		itemOption $itemOption,
		RequisitionListItem $requisitionListItem,
        ResourceHelper $resourceHelper,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Forix\Product\Helper\Data $dataHelper,
        \Forix\Config\Helper\Data $dataConfig
    )
    {
        $this->storeManager = $storeManager;
        $this->dataConfig = $dataConfig;
        $this->dataHelper = $dataHelper;
        $this->productRepository = $productRepository;
        $this->_radioSwatchHelper = $radioSwatchHelper;
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->helper = $helper;
        $this->baseHelper = $basehelper;
        $this->itemOption = $itemOption;
        $this->requisitionListItem = $requisitionListItem;
        $this->resourceHelper = $resourceHelper;
        $this->scopeConfigInterface = $scopeConfig;
        $this->_registry = $registry;
    }

    public function afterGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        $config
    )
    {
        /*** Edited by Hidro Le */
        $config = $this->jsonDecoder->decode($config);
        $config["chooseText"] = __("Please Select");
        $currentProduct = $subject->getProduct();
        /**
         *  Radio Options
         */
        if($this->_radioSwatchHelper->isProductHasRadioSwatch($currentProduct)) {
            $config['radioSwatch'] = $this->_radioSwatchHelper->getRadioSwatchConfig($currentProduct);
        }
        /**
         * Fitment Attributes
         */
        $fitMen = $this->processProductChildAttr($currentProduct);
        $config['fitment'] = $fitMen;
        $configRig = $this->processRigModels($currentProduct);
        /*** Edited by Hidro Le */
        if (!empty($configRig)) {
            $_tmpProducts = array_fill_keys(array_keys($fitMen), 0);
            $configAttribute = $config['attributes'];
            $i = 0;
            foreach ($configAttribute as $k => $item) {
                $i++;
                $configAttribute[$k]['position'] = $i;
                foreach ($item['options'] as $option){
                    foreach ($option['products'] as $_pid){
                        $_tmpProducts[$_pid]++;
                    }
                }
            }
            foreach ($configRig['options'] as $key => $option){
                foreach ($option['products'] as $_key => $_pid) {
                    if ($_tmpProducts[$_pid] == 0) {
                        unset($option['products'][$_key]);
                    }
                }
                if (count($option['products']) < 1) {
                    unset($configRig['options'][$key]);
                }
            }
            $newOptions = array_values($configRig['options']);
            $configRig['options'] = $newOptions;
            array_unshift($configAttribute, $configRig);
            $newAttr = [];
            foreach ($configAttribute as $k => $item) {
                $newAttr[$item["id"]] = $item;
            }
            $config['attributes'] = $newAttr;
        }

		$defaultValue = $this->setDefaultValue($config);
		if (!empty($defaultValue)) {
			$config["defaultValues"] = $defaultValue;
		}

        if (!empty($this->getValueSelectedRigModel())) {
        	$config["rigModelSelected"] = $this->getValueSelectedRigModel();
		}
        if (!empty($this->getValueOptionCutter())) {
			$config["optionCutter"] = $this->getValueOptionCutter();
		}


        $childProductList = $this->getChildProductCollection($currentProduct);
        foreach ($childProductList as $childProduct) {
            $productRes = $this->productRepository->getById($childProduct->getId());
            $config["checkstock"]['stock_' . $childProduct->getId()]['titleaddtocart'] = $this->dataHelper->isShowBackOrder($productRes);
            $config["checkstock"]['stock_' . $childProduct->getId()]['value'] = false;
            if($check = $this->dataHelper->checkInStock($productRes)) {
                $config["checkstock"]['stock_' . $childProduct->getId()]['value'] = $check;

            }

        }
		$config["is_required_rigmodel"] = $currentProduct->getData('required_rig_model');
		$config["rig_model_id"] = $this->helper->getIdAttribute('mb_rig_model');

        $config["in_stock_message"] = $this->dataConfig->getConfigValue("forix_catalog/stock/in_stock");
        $config["back_order_message"] = $this->dataConfig->getConfigValue("forix_catalog/stock/back_order");
        $config["url_distributor"] = $this->storeManager->getStore()->getBaseUrl() . $this->dataConfig->getConfigValue("amlocator/general/url");
        return $this->jsonEncoder->encode($config);
    }

    /**
     * Edited by Hidro Le
     * \Magento\Catalog\Model\Product
     * @param $currentProduct \Magento\Catalog\Model\Product
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    protected function getChildProductCollection($currentProduct)
    {
        if (!isset($this->_usedProducts[$currentProduct->getId()])) {
            /**
             * @var $configurable \Magento\ConfigurableProduct\Model\Product\Type\Configurable
             */
            $configurable = $currentProduct->getTypeInstance();
            $this->_usedProducts[$currentProduct->getId()] = $configurable->getUsedProducts($currentProduct);
        }
        return $this->_usedProducts[$currentProduct->getId()];
    }


    /**
     * Edited by Hidro Le
     * @param $currentProduct \Magento\Catalog\Model\Product
     * @return array
     */
    protected function processProductChildAttr($currentProduct)
    {
        $fitmenAttr = $this->helper->getFitMenAttribute();
        $result = [];
        $heavyWeightNum  = $this->scopeConfigInterface->getValue("forix_catalog/heavy/weight");

        foreach ($this->getChildProductCollection($currentProduct) as $product) {
            $child = $product->getId();
            foreach ($fitmenAttr as $attrCode => $attrLabel) {
                $attrC = $product->getAttributeText($attrCode);
                if (is_array($attrC)) {
                    $attrC = implode(", ", $attrC);
                }
                $result[$child][$attrLabel] = $attrC;
            }
            $result[$child]['sku']    = $product->getData('sku');
            if ($product->getWeight() > $heavyWeightNum) {
	            $result[$child]["show_heavy"] = true;
            }
        }

        return $result;
    }


    /**
     * Edited by Hidro Le
     * @param $currentProduct \Magento\Catalog\Model\Product
     * @return array
     */
    protected function processRigModels($currentProduct)
    {
    	$arrIdsRig = $ids = [];
    	foreach ($this->getChildProductCollection($currentProduct) as $_item) {
            $id = $_item->getEntityId();
            $ids[] = $id;
	        $idRig    = explode(",", $_item->getData('mb_rig_model'));
            if (!empty($idRig)) {
                foreach ($idRig as $_idRig) {
                    if(!isset($arrIdsRig[$_idRig])){
                        $arrIdsRig[$_idRig] = [];
                    }
                    if(!in_array($id,$arrIdsRig[$_idRig])){
                        array_push($arrIdsRig[$_idRig], $id);
                    }
                }
            }
        }
        

        if (!empty($arrIdsRig)) {
            $data = $this->resourceHelper->getOptionValueByIds(array_keys($arrIdsRig));

            $newArr = [];
            foreach ($data as $val) {
                array_push($newArr,[
                    'id'   => $val['option_id'],
                    'label'=> $val['value'],
                    'products'=>$arrIdsRig[$val['option_id']]
                ]);
            }

            if (!empty($newArr)) {
                $isRequiredRig = $currentProduct->getData('required_rig_model');
            	if ($isRequiredRig == 0) {
					$firstItem = [
						'id'   => 0,
						'label'=> __('All Rigs'),
						'products'=>$ids
					];
					array_unshift($newArr , $firstItem);
	            }

				$rigModel = [
					'id' => $this->helper->getIdAttribute('mb_rig_model'),
					'code'  => 'mb_rig_model',
					'label' => __('Your Rig Model'),
					'position' => 0,
					'options' => $newArr
				];

				return $rigModel;
			}
        }
        return [];

    }


	protected function setDefaultValue($config)
	{
		$out = [];
		if (isset($config["defaultValues"])) {
			$defaultValue = $config["defaultValues"];
			$params = $this->baseHelper->getParams();
			if (isset($params["rig_selected"])) {
				$rigSelected = $params["rig_selected"];
				$rigAttrId = array_keys($rigSelected)[0];
				$valueRig = $rigSelected[$rigAttrId];
				$out[$rigAttrId] = $valueRig;
				foreach ($defaultValue as $attr => $value) {
					$out[$attr] = $value;
				}
			}

			// For requisition list item
			if (!empty($this->rigRequisitionList())) {
				$rigSelected = $this->baseHelper->getParam('rig');
				$rigAttrId = array_keys($rigSelected)[0];
				$valueRig = $rigSelected[$rigAttrId];
				$out[$rigAttrId] = $valueRig;
				foreach ($defaultValue as $attr => $value) {
					$out[$attr] = $value;
				}
			}
		}

		return $out;
	}


    protected function getValueSelectedRigModel()
    {
		$out = [];
		$params = $this->baseHelper->getParams();
		if (isset($params["rig_selected"])) {
			$rigSelected = $params["rig_selected"];
			$rigAttrId = array_keys($rigSelected)[0];
			$valueRig = $rigSelected[$rigAttrId];
			$out[$rigAttrId] = $valueRig;
		}

		// For requisition list item
		if (!empty($this->baseHelper->getParam('rig'))) {
			$rigSelected = $this->baseHelper->getParam('rig');
			$rigAttrId = array_keys($rigSelected)[0];
			$valueRig = $rigSelected[$rigAttrId];
			$out[$rigAttrId] = $valueRig;
		}

        return $out;
    }

    protected function getValueOptionCutter()
	{
		$out = [];
		if ($this->baseHelper->getFullActionName() == "checkout_cart_configure") {
			$id = $this->baseHelper->getParam("id");
			$op = $this->itemOption->getInfoRequestByQuoteId($id);
			if (isset($op["value"])) {
				$val = $op["value"];
				$arrVal = $this->jsonDecoder->decode($val);
				if (isset($arrVal["options"]) && !empty($arrVal["options"])) {
					foreach ($arrVal["options"] as $k => $item) {
						$out[$k] = $item;
					}
				}
			}
		}
		return $out;
	}


	// proccess rigModel for Requisition List

	protected function rigRequisitionList() {
    	$out = [];
    	if ($this->baseHelper->getFullActionName() == "requisition_list_item_configure") {
			$requisitionListItem = $this->requisitionListItem->load($this->baseHelper->getParam("item_id"));
			if (isset($requisitionListItem["options"]["info_buyRequest"]["rig"])) {
				$rigSelected = $requisitionListItem["options"]["info_buyRequest"]["rig"];
				$rigAttrId = array_keys($rigSelected)[0];
				$valueRig = $rigSelected[$rigAttrId];
				$out[$rigAttrId] = $valueRig;
			}
			if (!empty($out)) {
				$this->baseHelper->setParams(["rig"=>$out]);
			}
		}
		return $out;
	}


}