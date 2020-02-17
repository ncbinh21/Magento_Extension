<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 15/08/2018
 * Time: 14:17
 */

namespace Forix\CategoryCustom\Block\Category;
class View extends \Forix\CategoryCustom\Block\GroundCategory
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $jsLayout = $this->jsLayout;
        $filter_ground_condition = [
            'url' => $this->getCurrentCategory()->getUrl(),
            'name' => $this->getCurrentCategory()->getName()
        ];
        $currentGround = $this->getCurrentGroundInfo();
        if (false !== $currentGround) {
            $currentUrl = $this->_urlBuilder->getCurrentUrl();
            $filter_ground_condition['url'] = $currentUrl;
            $filter_ground_condition['name'] = $currentGround['name'];

            $this->_session->setData('filter_ground_condition', [
                'current_ground_condition' => $filter_ground_condition
            ]);

            if(strpos($currentUrl, 'ground-condition') !== false) {
                $type = 'canonical';
                $canonicalUrl = $currentUrl;
                if($oder =  strpos($canonicalUrl, '?')) {
                    $canonicalUrl = substr($canonicalUrl, 0, $oder);
                }
                $this->pageConfig->addRemotePageAsset(
                    html_entity_decode($canonicalUrl),
                    $type,
                    ['attributes' => ['rel' => $type]]
                );
            }
        }
        $jsLayout['components']['register_ground_filter']['config']['filter_ground_condition'] = $filter_ground_condition;
        $this->jsLayout = $jsLayout;
    }
}