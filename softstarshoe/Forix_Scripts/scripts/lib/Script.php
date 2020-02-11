<?php

require_once("../vendor/magento/framework/DataObject.php");
require_once("core/Util/Log.php");
require_once("myprocess/CreateStaticBlock.php");
require_once("myprocess/CreatePage.php");

class Henry
{
    const MODE_COMMANDLINE = "MODE_COMMANDLINE";
    const MODE_WEB = "MODE_WEB";

    protected static $_mode = "MODE_WEB";
    protected static $_instance = null;

    /**
     * Singleton instance
     * @return App
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function init($isCommandLine, $request)
    {
        self::$_mode = $isCommandLine ? self::MODE_COMMANDLINE : self::MODE_WEB;
        self::initRequest($request);
    }

    /**
     * @return string
     */
    public static function getMode()
    {
        return self::$_mode;
    }

    public static function isCommanLineMode()
    {
        return self::$_mode == self::MODE_COMMANDLINE;
    }

    private static $REQUEST = null;

    /**
     * @return Core_Request
     */
    public static function getRequest()
    {

        if (self::$REQUEST == null) {
            self::$REQUEST = new \Magento\Framework\DataObject();
        }
        return self::$REQUEST;
    }

    public static function initRequest($data)
    {
        if (self::$_mode == self::MODE_COMMANDLINE) {
            unset($data[0]);
            $tmp["process"] = $data[1];
            unset($data[1]);
            for ($i = 2; $i <= count($data); $i = $i + 2) {
                $tmp[str_replace("-", "", $data[$i])] = $data[$i + 1];
            }
            self::getRequest()->setData($tmp);
        } else {
            self::getRequest()->setData($data);
        }
    }

    /**
     * @param $processClass
     * @return Core_Process_Abstract
     */
    public static function createProcess($processClass)
    {
        if (class_exists($processClass)) {
            $InstanceClass = new $processClass();
            if($InstanceClass instanceof Core_Process_Abstract) return $InstanceClass;
            echo "\n\nThis Process [$processClass] must extend [Core_Process_Abstract]!\n\n";
            exit();
        } else {
            echo "\n\nThis Process [$processClass] not exists!\n\n";
            exit();
        }
    }

    public static function logMonitor($message)
    {
        Core_Util_Log::logMonitor($message);
    }

    public static function logError($message)
    {
        Core_Util_Log::logError($message);
    }

    public static function breakLineInLog()
    {
        Core_Util_Log::breakLineInLog();
    }

    public static function getLibDir(){
        return realpath(dirname(__FILE__));
    }
}