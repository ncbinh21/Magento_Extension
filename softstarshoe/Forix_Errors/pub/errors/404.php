<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

function array_delete_by_key(&$array, $delete_key, $use_old_keys = FALSE) {

    unset($array[$delete_key]);

    if(!$use_old_keys) {
        $array = array_values($array);
    }

    return TRUE;
}
function factorial($int){
    if($int < 2) {
        return 1;
    }

    for($f = 2; $int-1 > 1; $f *= $int--);

    return $f;
}

function perm($arr, $nth = null) {

    if ($nth === null) {
        return perms($arr);
    }

    $result = array();
    $length = count($arr);

    while ($length--) {
        $f = factorial($length);
        $p = floor($nth / $f);
        $result[] = $arr[$p];
        array_delete_by_key($arr, $p);
        $nth -= $p * $f;
    }

    $result = array_merge($result,$arr);
    return $result;
}

function perms($arr) {
    $p = array();
    $f = factorial(count($arr));
    for ($i=0; $i < $f; $i++) {
        $p[] = perm($arr, $i);
    }
    return $p;
}
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/../../../../html/app/bootstrap.php';
$maxQueryNum = 3;
$bootstrap = Bootstrap::create(BP, $_SERVER);
$obj = $bootstrap->getObjectManager();
$obj->get('\Magento\Framework\App\State')->setAreaCode('frontend');
$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');
$urlFinder = $obj->get('\Magento\UrlRewrite\Model\UrlFinderInterface');
$url = $obj->get('\Magento\Framework\UrlInterface');
$request = $obj->get('\Magento\Framework\App\RequestInterface');
$requestUri = parse_url($_SERVER['REQUEST_URI']);
if(!empty($requestUri['query'])){
    $query = explode('&',urldecode($requestUri['query']));
    if(count($query) <= $maxQueryNum){
        $queryShuffle = perms($query);
        $pathInfo = $request->getPathInfo();
        $requestPaths = [];
        foreach($queryShuffle as $query){
            array_push($requestPaths,array('like'=>trim($pathInfo.'?'.implode('&',$query),'/')));
        }
        $rewrite = $obj->create('\Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection')
            ->addFieldToFilter('request_path',$requestPaths)
            ->addFieldToFilter('store_id',array('eq'=>$storeManager->getStore()->getId()))
            ->setPageSize(1)
            ->getFirstItem();
    }
}

if(!isset($rewrite) || is_null($rewrite) || !is_object($rewrite) || (is_object($rewrite) && !$rewrite->getId())){
    $rewrite = $urlFinder->findOneByData([
        'request_path' => trim($request->getPathInfo(), '/'),
        'store_id' => $storeManager->getStore()->getId(),
    ]);
}
if ($rewrite !== null) {
    if($rewrite->getEntityType() =='custom' && $rewrite->getRedirectType() == 301){
        $target = $rewrite->getTargetPath();
        if (($prefix = substr($target, 0, 6)) !== 'http:/' && $prefix !== 'https:') {
            $target = $url->getUrl('', ['_direct' => $target]);
        }
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: {$target}");
        exit();

    }
}


require_once getcwd() . '/processorFactory.php';

$processorFactory = new \Magento\Framework\Error\ProcessorFactory();
$processor = $processorFactory->createProcessor();
$response = $processor->process404();
$response->sendResponse();
