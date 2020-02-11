<?php

require_once (__DIR__ . "/../core/Process/Abstract.php");
require_once (__DIR__ . "/../core/StaticBlock/Creator.php");

class CreateStaticBlock extends Core_Process_Abstract {
    protected $monitorLogFile = "create_static_block_monitor.log";
    protected $errorLogFile   = "create_static_block_error.log";
    protected $processName = "CREATE STATIC BLOCK";

    protected function process(){
        $dirs = scandir($this->getDataDir());
        foreach($dirs as $file){
            $fullFilePath = $this->getDataDir()."/".$file;
            if(is_file($fullFilePath)){
                $this->importBlock($fullFilePath,$file);
            }
        }

        echo "Finished!!!";
    }

    private function getDataDir(){
        return Henry::getLibDir()."/data/blocks";
    }

    private function importBlock($file,$fileName){
        $fileContent = file_get_contents($file);
        $pattern = '/<!--\n?\[\[Header\]\]:(.*)\n?-->/';
        preg_match($pattern, $fileContent, $matches);
        $title = $matches[1];
        $fileContent = preg_replace($pattern,"",$fileContent);
        $fileContent = preg_replace("/<!--(.*?)-->/Uis","",$fileContent);
        try{
            $key = str_replace(".phtml","",$fileName);
            $result = Core_StaticBlock_Creator::createBlock($title,$key,$fileContent);
            if($result == 1){
                echo ("Update [$title] Key [$key]");
                echo ("Update [$title] Key [$key]");
            }else{
                echo ("Insert [$title] Key [$key]");
            }
        }catch (Core_Exception $ex){
            echo ($ex->getMessage());
            echo ("Error where import block: $title Key $key");
        }

    }
}