<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: melfredborzall.local
 */

namespace Forix\IntelliSuggest\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getJsSrc(){
        return $this->scopeConfig->getValue('intellisuggest_tracking/general/js_src');
    }
    public function getSiteId(){
        return $this->scopeConfig->getValue('intellisuggest_tracking/general/site_id');
    }
}