<?php

class Core_Util_Log
{
    private static $MONITOR_LOG_FILE = "";
    private static $ERROR_LOG_FILE   = "";

    public static function logMonitor($message)
    {
        echo $message;
    }

    public static function logError($message)
    {
        if(Henry::isCommanLineMode()){
            echo "\n\t|ERROR ------------------------------------------------";
            echo "\n\t|MESSAGE: ".$message."\n\t|Please see: ".self::$ERROR_LOG_FILE." log file!";
            echo "\n\t ------------------------------------------------------\n\n";
        }
        echo $message;
    }

    public static function breakLineInLog()
    {
        echo "=================================";
    }

    public static function setErrorLogFile($fileName)
    {
        self::$ERROR_LOG_FILE = $fileName;
    }

    public static function setMonitorLogFile($fileName)
    {
        self::$MONITOR_LOG_FILE = $fileName;
    }
}