<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 17/09/2018
 * Time: 12:34
 */

namespace Forix\ProductWizard\Block;


class Image extends \Forix\ProductWizard\Block\Wizard
{
    public function getBaseImageUrl(){
        return $this->getCurrentWizard()->getImageUrl('base_image');
    }
    public function getBannerImageUrl(){
        return $this->getCurrentWizard()->getImageUrl('banner_image');
    }
}