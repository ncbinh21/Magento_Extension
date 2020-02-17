<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: html
 */
namespace Forix\Megamenu\Rewrite\Ves\Megamenu\Helper;

use Magento\Framework\App\ObjectManager;
use Forix\Base\Helper\Data as BaseHelper;
use Magento\Framework\Json\EncoderInterface;
use Forix\Shopby\Helper\Data as ShopByHelper;
use Magento\Eav\Model\Config;

class Data extends \Ves\Megamenu\Helper\Data {


	protected $_eavCollection;
	protected $_baseHelper;
	protected $_jsonEncoder;
	protected $_shopbyHelper;
	protected $_config;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		\Magento\Cms\Model\Template\Filter $filter,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Escaper $escaper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Customer\Model\Group $groupManager,
		\Ves\Megamenu\Model\Config\Source\StoreCategories $storeCategories,
		\Magento\Framework\Url $url,
		BaseHelper $baseHelper,
		EncoderInterface $encoder,
		ShopByHelper $shopbyHelper,
		Config $config
	) {
		parent::__construct($context,$filterProvider,$filter,$registry,$escaper,$storeManager,$categoryFactory,$groupManager,$storeCategories,$url);
		$this->_baseHelper   = $baseHelper;
		$this->_jsonEncoder  = $encoder;
		$this->_shopbyHelper = $shopbyHelper;
		$this->_config 		 = $config;
	}


	public function forixDrawAnchor($item){
        $html = $class= '';
        // Custom Link, Category Link
        $href = '';
        if($item['link_type'] == 'custom_link'){
            $href = $this->filter($item['link']);
            if($this->endsWith($href, '/')){
                $href = substr_replace($href, "", -1);
            }
        }elseif($item['link_type'] == 'attribute_link'){
            $baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
            $cat = $this->getCategory($item['category']);//$cats->getItemById($item['category']);

            $query = array();
            if(!empty($item['attribute_code']) && !empty($item['attribute_value'])){
                $query = array($item['attribute_code'] => $item['attribute_value']);
            }
            $href = isset($cat['url'])?$this->_urlBuilder->getUrl(null, ['_direct'=>str_replace($baseUrl,'',$cat['url']), '_query' => $query]):'';
        }elseif($item['link_type'] == 'category_link'){
            if ($category = $this->getCategory($item['category'])) {
                $href = $category['url'];
                if($urls = parse_url($href)){
                    $url_host = $urls['host'];
                    $base_url = $this->_storeManager->getStore()->getBaseUrl();
                    if($base_urls = parse_url($base_url)) {
                        if($url_host != $base_urls['host']){
                            $href = str_replace($url_host, $base_urls['host'], $href);
                        }
                    }
                }
            }
        }
        if(isset($item['level']) && $item['level'] == 0){
            $class = 'class="level-top"';
        }

        $target = $item['target']?'target="' . $item['target'] . '"':'';
        if($href=='') $href = '#';
        if($href == '#') $target = '';
        $html .= '<a href="' . $href . '" ' . $target . ' ' . $class . '>';

        if($item['show_icon']){
            $html .= '<i class="' .$item['icon_classes'] . '"></i>';
        }

        // Icon Left
        if($item['show_icon'] && $item['icon_position']=='left' && $item['icon']!=''){
            $html .= '<img class="icon-left" src="'.$item['icon'].'" alt="'.$item['name'].'"/>';
        }

        if($item['name']!=''){
            $html .= '<span>' . $item['name'] . '</span>';
        }

        // Icon Right
        if($item['show_icon'] && $item['icon_position']=='right' && $item['icon']!=''){
            $html .= '<img class="icon-right" src="'.$item['icon'].'" alt="'.$item['name'].'"/>';
        }

        $html .= '</a>';
        return $html;
    }

    protected function _getMenuItemClasses($item)
    {
        $classes = [];
        if(isset($item['level'])){
            $classes[] = 'level' . $item['level'];
            if($item['level'] == 0) {
                $classes[] = 'level-top';
            }
        }
        if(isset($item['position_class']))
        $classes[] = $item['position_class'];

        if (isset($item['is_first']) && $item['is_first'] == true) {
            $classes[] = 'first';
        }

        if (isset($item['active']) && $item['active'] == true) {
            $classes[] = 'active';
        }

        if (isset($item['is_last']) && $item['is_last'] == true) {
            $classes[] = 'last';
        }

        if (isset($item['classes'])) {
            $classes[] = $item['classes'];
        }

        if (isset($item['has_children']) && $item['has_children'] == true) {
            $classes[] = 'parent';
        }

        return $classes;
    }

    public function forixDrawItem($item, $level = 0, $x = 0, $listTag = true){
        $hasChildren = false;
        $item['level'] = $level;
        $item['position_class'] = 'nav-' . $x;
        if(!isset($item['status']) || (isset($item['status']) && !$item['status'])) return;
        if(isset($item['children']) && count($item['children'])>0) {
            $item['has_children'] = $hasChildren = true;
        }
        // Custom Link, Category Link
        $href = '';

        if($item['link_type'] == 'custom_link'){
            $href   = $item['link'];
        }elseif($item['link_type'] == 'category_link'){
            if ($category = $this->getCategory($item['category'])) {
                $href = $category['url'];
                if($urls = parse_url($href)){
                    $url_host = $urls['host'];
                    $base_url = $this->_storeManager->getStore()->getBaseUrl();
                    if($base_urls = parse_url($base_url)) {
                        if($url_host != $base_urls['host']){
                            $href = str_replace($url_host, $base_urls['host'], $href);
                        }
                    }
                }
            }
        }

        $link = $this->filter($href);
        $link = trim($link);

        if($this->endsWith($link, '/')){
            $link = substr_replace($link, "", -1);
        }
        $currentUrl = trim($this->_url);
        $currentUrl = $this->filter($currentUrl);

        if($this->endsWith($currentUrl, '/')){
            $currentUrl = substr_replace($currentUrl, "", -1);
        }
	    $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	    // $_objectManager->create('\Psr\Log\LoggerInterface')->debug($currentUrl);
		$urlInterFace = $_objectManager->get('Magento\Framework\UrlInterface');
		$currentLink  = $urlInterFace->getCurrentUrl();

        if($link == $currentLink && ($href != '' && $href!='#')){
            // $item['active'] = true;
        }

        $classes   = $this->_getMenuItemClasses($item);
		$start     = strpos($item['image_hover'],'/media');
		$dataHover = substr($item['image_hover'], $start);
		if (is_file('./'.$dataHover))  {
			$dataHover  = rtrim($this->getBaseUrl(),"/").$item['image_hover'];
		} else {
			$dataHover  = rtrim($this->getBaseUrl(),"/").'/pub'.$item['image_hover'];
		}
        $html = '<li data-hover="'.$dataHover.'" data-link="'.$link.'" class="top-link-megamenu '.implode(' ',$classes).'">';
        $html .= $this->forixDrawAnchor($item);
        if($hasChildren){
            $subClass = 'level'.$level;
            $level++;
            $subClass .= ' submenu';
            $subClass = 'class="' . $subClass . '"';
            $html .= '<ul ' . $subClass .'>';
            if ($item['image_hover']!="")
            {

				$start    = strpos($item['image_hover'],'/media');
				$imgHover = substr($item['image_hover'], $start);
				if (is_file('./'.$imgHover)) {
					$imgHover = rtrim($this->getBaseUrl(),"/").$imgHover;
				} else if (is_file('./pub/'.$imgHover)) {
					$imgHover = rtrim($this->getBaseUrl(),"/").'/pub'.$item['image_hover'];
				}

				$html .= '<div class="left-category-thumbnail" data-hover="'.$imgHover.'">
							<img src="'.$imgHover.'" />
						 </div>';
			}

            if($hasChildren){
                $children = $item['children'];
                $i = 1;
                $lastIndex = count($children);
                $html.= '<div class="submenu-warp"><div class="submenu-inner"><li class="grid-sizer-menu"></li>';
                foreach ($children as  $z => $it) {
                    if($i == 1){
                        $it['is_first'] = true;
                    }
                    if($i == $lastIndex){
                        $it['is_last'] = true;
                    }
                    // RIGHT SIDEBAR BLOCK
                    $html .= $this->filter($this->forixDrawItem($it, $level, $x.'-'.$i, true));
                    $i++;
                }
				$html.= '</div></div>';
            }

            // RIGHT SIDEBAR BLOCK
            $right_sidebar_width = isset($item['right_sidebar_width'])?$item['right_sidebar_width']:0;
            if($item['show_right_sidebar'] && $item['right_sidebar_html']!=''){

            	if($right_sidebar_width) $right_sidebar_width = 'style="width:' . $right_sidebar_width . '"';
                $html .= '<div class="right-block-submenu"><li class="category-right-block"><div class="megamenu-sidebar right-sidebar" '.$right_sidebar_width.'>'.$this->decodeWidgets($item['right_sidebar_html']).'</div></div></li>';
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
        $html= $this->decodeImg($html);
        return $html;
    }
    public function renderMenu($menu){
        $html = '';
        $menuItems = $menu->getData('menuItems');
        $structure = json_decode($menu->getStructure(), true);

        $categories = [];
        foreach ($menuItems as $item) {
            if (isset($item['link_type']) && ($item['link_type'] == 'category_link' || $item['link_type'] == 'attribute_link') && isset($item['category']) && !in_array($item['category'], $categories)) {
                $categories[] = $item['category'];
            }
        }


        $this->setMenuCategories($categories);
        if(is_array($structure)){
            $lastIndex = count($structure) - 1;
            foreach ($structure as $k => $v) {
                $itemData = $this->renderMenuItemData($v, [], $menuItems);
                if($k == 0){
                    $itemData['is_first'] = true;
                }
                if($k == $lastIndex){
                    $itemData['is_last'] = true;
                }
                $html .= $this->forixDrawItem($itemData,0,$k+1);
            }
        }
        return $html;
    }

	public function getBaseUrl()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$url = $storeManager->getStore()->getBaseUrl();
		$url = str_replace("index.php","",$url);
		return $url;
	}

	public function getRigModel()
	{
	    //Hidro Remove.
		$ojectManager = ObjectManager::getInstance();
		$eav = $ojectManager->get('Forix\Shopby\Model\ResourceModel\ResourceHelperFactory')->create()->getOptionIdByCode('mb_rig_model');
		$collectionFactory = $ojectManager->get('Forix\AdvancedAttribute\Model\ResourceModel\Option\CollectionFactory')->create();
		$collectionFactory->addFieldToFilter('attribute_id', $eav['attribute_id']);
		$options = [];
		$mafufacture = $this->getAllManuFacturer();
		foreach ($collectionFactory as $_item) {
			$mbManufacturer = $_item->getData('mb_oem');
			$label = "";
			foreach ($mafufacture as $_itemManu) {
				if ($mbManufacturer == $_itemManu["value"]) {
					$label = $_itemManu["label"];
				}
			}

			if ($_item->getData('name')!="") {
				$options[] = [
					'label'        => $_item->getData('name'),
					'manufacture'  => $label
				];
			}
		}

		$rigOptions = [];
		if (!empty($options)) {
			$fullActionName = $this->_baseHelper->getFullActionName();
			$rigOptions = $options;
			if ($fullActionName == "cms_index_index") {
				$options = $this->_jsonEncoder->encode($options);
				$this->_shopbyHelper->setRigModelRegister($options);
			}
		}

		return $rigOptions;
	}

	protected function getAllManuFacturer()
	{
	    //Hidro Le Remove
		$attribute = $this->_config->getAttribute('catalog_product', 'mb_oem');
		$options   = $attribute->getSource()->getAllOptions();
		return $options;
	}


}