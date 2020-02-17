<?php

namespace Forix\Shopby\Controller\Rig;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Forix\Base\Helper\Data;
use Forix\Shopby\Helper\Data as shopByHelper;


class Index extends \Magento\Framework\App\Action\Action {

    protected $pageFactory;
    protected $redirectFactory;
    protected $collection;
    protected $helper;
    protected $shopbyData;
    protected $filterManager;
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        \Forix\Shopby\Model\ResourceModel\ResourceHelperFactory $resourceHelperFactory,
        Data $helper,
        shopByHelper $shopbyHelper,
        \Magento\Framework\Filter\FilterManager $filterManager
    )
    {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
        $this->redirectFactory = $redirectFactory;
        $this->resourceHelperFactory = $resourceHelperFactory;
        $this->helper = $helper;
        $this->shopbyData = $shopbyHelper;
        $this->filterManager = $filterManager;
    }

    public function execute()
    {
        $params = $this->_request->getParams();
        // $manuItem= array_keys($params)[0];
        // $k =  str_replace(" ","-",urldecode($manuItem));
        // $params[$k] = $params[$manuItem];
        // unset($params[$manuItem]);

        $manufacture = $this->getFullOptions();
        $itemRig = $itemManufacture = '';
        $flag = false;


        foreach ($manufacture as $key => $_item) {
            $_itemCheck = $this->filterManager->translitUrl($_item);
            if (isset($params[$_itemCheck])) {
                $itemRig = $params[$_itemCheck];
                $itemManufacture = $key;
                $nameItemManufacture = $_item;
                $flag = true;
                break;
            }
        }

        if (!$flag) {
            return $this->redirectFactory->create()
                ->setPath('noroute');
        }
        $resourceHelper = $this->resourceHelperFactory->create();
        //$itemRig    = $this->shopbyData->RevertParam($itemRig); Edit by Hidro
        $itemRigId = substr($itemRig,strrpos($itemRig,'-')+1);
        $collection = $resourceHelper->getOptionById($itemRigId);


        if (!$collection) {
            return $this->redirectFactory->create()
                ->setPath('noroute');
        }

        $manufacturerItem = $resourceHelper->getBanner($itemManufacture);

        $banner = '';
        if (isset($manufacturerItem['icon_hover'])) {
            $banner = $this->helper->getMediaUrl().'attribute/banners/image'.$manufacturerItem['icon_hover'];
        }
        $this->_request->setParams(
            [
                'rig_options'  => $collection,
                'rig_title'    => $collection['value'],
                'manufacturer' => $nameItemManufacture,
                'banner'       => $banner,
                'border_color' => isset($manufacturerItem["mb_manufacturer_color"]) ? $manufacturerItem["mb_manufacturer_color"] : "#FEC53B",
                'mb_rig_model' => $collection['option_id']
            ]
        );
        return $this->pageFactory->create();
    }

    protected function getOptions() {
        $attribute = $this->_objectManager->get("\Magento\Eav\Model\Config")->getAttribute('catalog_product', 'mb_oem');
        $options = $attribute->getSource()->getAllOptions();
        unset($options[0]);
        $attrOptions = [];
        foreach ($options as $k=>$_option) {
            if ($_option['label']!="") {
                $attrOptions[] = strtolower(str_replace(['%20',' ','/'],['-','-','_'], $_option['label']));
            }
        }
        return $attrOptions;
    }

    protected function getFullOptions() {
        $attribute = $this->_objectManager->get("\Magento\Eav\Model\Config")->getAttribute('catalog_product', 'mb_oem');
        $options = $attribute->getSource()->getAllOptions();
        unset($options[0]);
        $attrOptions = [];
        foreach ($options as $k=>$_option) {
            if (isset($_option['value']) && $_option['value']!="" && isset($_option['label']) && $_option['label']!="") {
                $attrOptions[$_option['value']] = $_option['label'];
            }
        }
        return $attrOptions;
    }


}