<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: html
 */

namespace Forix\Megamenu\Rewrite\Ves\Megamenu\Block;


class Menu extends \Ves\Megamenu\Block\Menu
{
    public function translitUrl($value){
        return $this->filterManager->translitUrl($value);
    }
	public function _toHtml(){
		if(!$this->getTemplate()){
			$this->setTemplate("Ves_Megamenu::widget/menu.phtml");
		}
		$store = $this->_storeManager->getStore();
		$menu = '';
		if ($menuId = $this->getData('id')) {
			$menu = $this->_menu->setStore($store)->load((int)$menuId);
			if ($menu->getId() != $menuId) {
				return;
			}
		}elseif($alias = $this->getData('alias')){
			$menu = $this->_menu->setStore($store)->load(addslashes($alias));
			if ($menu->getAlias() != $alias) {
				return;
			}
		}
		if($menu && $menu->getStatus()){
			$this->setData("menu", $menu);
		}
		return \Magento\Framework\View\Element\Template::_toHtml();
	}
}