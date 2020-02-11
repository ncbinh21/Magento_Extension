<?php


abstract class Core_Process_Abstract
{
    protected $monitorLogFile = "default_process_monitor.log";
    protected $errorLogFile   = "default_process_error.log";
    protected $processName = "PROCESS";
    private $_request;

    private function printHeader()
    {
        if (Henry::isCommanLineMode()) {
            echo "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
            echo "\n\n=========== START: " . $this->processName . " =============+";
            echo "\n+Time             : ".date("m-d-Y H:i:s");
            echo "\n+Database         : " . (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname') ;
            echo "\n+Monitor Log File : " . $this->monitorLogFile ;
            echo "\n+Error Log File   : " . $this->errorLogFile ;
            echo "\n-----------------------------------------------------------";
            echo "\n\n";
        }
        Henry::logMonitor("START: ".$this->processName);
        Henry::logMonitor("TIME: " . date("m-d-Y H:i:s"));
        Henry::breakLineInLog();
    }

    private function printFooter()
    {
        if (Henry::isCommanLineMode()) {
            echo "\n\n-----------------------------------------------------------";
            echo "\n=========== END: " . $this->processName . " ===============+\n";
            echo "+Time: ".date("m-d-Y H:i:s")."\n\n";
        }
        Henry::breakLineInLog();
        Henry::logMonitor("END: " . $this->processName);
        Henry::logMonitor("TIME: " . date("m-d-Y H:i:s"));
    }

    public function __construct()
    {
        Core_Util_Log::setMonitorLogFile($this->monitorLogFile);
        Core_Util_Log::setErrorLogFile($this->errorLogFile);

        $this->printHeader();
        $this->_request = Henry::getRequest();
        $this->_construct();
    }

    protected function getRequest()
    {
        return $this->_request;
    }

    abstract protected function process();

    protected function _construct(){}

    final public function run()
    {
        try{
            $this->process();
        }catch (Exception $ex){
            Henry::logMonitor($ex->getMessage());
            Henry::logError($ex);
        }

        $this->printFooter();
    }
}