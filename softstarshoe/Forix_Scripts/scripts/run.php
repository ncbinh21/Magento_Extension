<?php
date_default_timezone_set("America/Chicago");
require __DIR__ . '/../../../html/app/bootstrap.php';

$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('Magento\Framework\App\Http');
//$bootstrap->run($app);

//=======+ DON'T CHANGE THIS CODE +==============================
$isCommandLineMode = false;                                    //
                                                               //
if(defined('STDIN')){                                          //
    parse_str(implode('&', array_slice($argv, 1)), $_GET);     //
    $isCommandLineMode = true; // Commandline mode             //
}else{                                                         //
    $argv = $_GET;                                             //
}                                                              //
require_once realpath(dirname(__FILE__)) . '/lib/Script.php';   //
Henry::init($isCommandLineMode,$argv);                         //
//===============================================================

$ob = Henry::createProcess(Henry::getRequest()->getProcess());
$ob->run();



