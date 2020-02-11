<?php
require_once(__DIR__ . "/../../../../../../html/vendor/magento/framework/App/ObjectManager.php");

class Core_StaticBlock_Creator
{

    /**
     * @param string $title
     * @param string $ident
     * @param string $content
     * @param bool $overwrite
     * @return int
     * @throws Core_Exception
     */
    public static function createBlock($title, $ident, $content, $overwrite = true)
    {
        $title = trim($title);
        $ident = trim($ident);
        /* @var $block Mage_Cms_Model_Block */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Cms\Model\Block $block */
        $block = $om->create('Magento\Cms\Model\Block');
        $block->load($ident, 'identifier');
        //$block = Mage::getModel("cms/block")->load($ident,"identifier");
        if ($block->getId()) {
            if ($overwrite == true) {
                try{
                    $block->setContent($content);
                    $block->setTitle($title);
                    $block->save();
                    return 1;
                }catch (Zend_Db_Adapter_Exception $ex){
                    throw new Core_Exception($ex->getMessage());
                }
            }
        } else {
            try{
                $block->setContent($content);
                $block->setTitle($title);
                $block->setIdentifier($ident);
                $block->setStores(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
                $block->save();
                return 2;
            }catch (Zend_Db_Adapter_Exception $ex){
                throw new Core_Exception($ex->getMessage());
            }
        }
    }

}