<?php

require_once(__DIR__ . "/../../../../../../html/vendor/magento/framework/App/ObjectManager.php");

class Core_Page_Creator
{

    /**
     * @param $title
     * @param $key
     * @param $content_heading
     * @param $content
     * @param string $layout
     * @param string $layout_xml
     * @return int
     * @throws Core_Exception
     */
    public static function createPage($title, $key,  $content_heading , $content, $layout = 'one_column', $layout_xml = '')
    {
        /* @var $page Mage_Cms_Model_Page */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $page = $om->create('Magento\Cms\Model\Page');
        $page = $page->load($key,"identifier");
        $page->setTitle($title);
        $page->setContentHeading($content_heading);
        $page->setContent($content);
        $page->setLayoutUpdateXml($layout_xml);
        $page->setRootTemplate($layout);
        $page->setStores(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        if($page->getId()){
            $state = 1;
        }else{
            $page->setIdentifier($key);
            $state = 2;
        }
        try{
            $page->save();
            return $state;
        }catch (Zend_Db_Adapter_Exception $ex){
            throw new Core_Exception($ex->getMessage());
        }
    }
}