<?php

class CronTmp extends Core_Process_Abstract {
    protected $processName = "Create";
    protected function process(){
        Mage::getModel("sarp/cron")->process();
        echo "Cron Finished";
    }
}