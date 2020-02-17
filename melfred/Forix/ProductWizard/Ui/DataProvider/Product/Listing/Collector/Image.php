<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 30/08/2018
 * Time: 11:10
 */

namespace Forix\ProductWizard\Ui\DataProvider\Product\Listing\Collector;


use Magento\Catalog\Api\Data\ProductRender\ImageInterfaceFactory;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Framework\App\State;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\StoreManagerInterface;

class Image extends \Magento\Catalog\Ui\DataProvider\Product\Listing\Collector\Image
{
    public function __construct(
        ImageFactory $imageFactory,
        State $state,
        StoreManagerInterface $storeManager,
        DesignInterface $design,
        ImageInterfaceFactory $imageRenderInfoFactory,
        array $imageCodes = [])
    {
        foreach ($imageCodes as $key => $imageCode){
            if(!$imageCode){
                unset($imageCodes[$key]);
            }
        }
        parent::__construct($imageFactory, $state, $storeManager, $design, $imageRenderInfoFactory, $imageCodes);
    }
}