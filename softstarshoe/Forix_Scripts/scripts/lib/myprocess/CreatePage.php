<?php

require_once (__DIR__ . "/../core/Process/Abstract.php");
require_once (__DIR__ . "/../core/Page/Creator.php");

class CreatePage extends Core_Process_Abstract {
    protected $monitorLogFile = "create_page_monitor.log";
    protected $errorLogFile   = "create_page_error.log";
    protected $processName    = "CREATE PAGE";

    protected function process(){
        $dirs = scandir($this->getDataDir());
        foreach($dirs as $file){
            $fullFilePath = $this->getDataDir().$file;
            if(is_file($fullFilePath)){
                if(substr($file,strlen($file)-6,6) == ".phtml"){
                    $this->importPage($fullFilePath,$file);
                }
            }
        }
        echo "Finished!!!";

    }

    private function getDataDir(){
        return Henry::getLibDir()."/data/pages/";
    }

    private function importPage($file,$fileName){
        $fileContent = file_get_contents($file);
        $pattern = '/<!--\n?\[\[PageTitle\]\]:(.*)\n?\[\[ContentHeading\]\]:(.*)\n?\[\[Layout\]\]:(.*)\n?\[\[URLKey\]\]:(.*)\n-->/';
        preg_match($pattern, $fileContent, $matches);
        $pageTitle = $matches[1];
        $contentHeading = $matches[2];
        $layout = $matches[3];
        $pageContent = preg_replace($pattern,"",$fileContent);
        $pageContent = preg_replace("/<!--(.*?)-->/Uis","",$pageContent);

        $key = str_replace(".phtml","",$fileName);
        $layoutUpdateXml = '';
        if(file_exists($this->getDataDir().$key.".xml")){
            $layoutUpdateXml = file_get_contents($this->getDataDir().$key.".xml");
            $layoutUpdateXml = preg_replace("/<!--(.*?)-->/Uis","",$layoutUpdateXml);
        }
        try{
            $result = Core_Page_Creator::createPage($pageTitle,$key,$contentHeading,$pageContent,$layout,$layoutUpdateXml);
            if($result == 1){
                echo ("Update [$pageTitle] Key [$key]");
                echo ("Update [$pageTitle] Key [$key]");
            }else{
                echo ("Insert [$pageTitle] Key [$key]");
                echo ("Insert [$pageTitle] Key [$key]");
            }
        }catch (Core_Exception $ex){
            echo ($ex->getMessage());
            echo ("Error where import page: $pageTitle Key $key");
        }
    }
}