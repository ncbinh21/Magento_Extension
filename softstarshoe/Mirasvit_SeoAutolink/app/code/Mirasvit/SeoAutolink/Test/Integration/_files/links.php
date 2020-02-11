<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



require 'testsuite/Magento/Store/_files/core_fixturestore.php';

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\App\ResourceConnection $installer */
$installer = $objectManager->create('Magento\Framework\App\ResourceConnection');
$installer->getConnection()->query(
    'DELETE FROM '.$installer->getTableName('mst_seoautolink_link').' WHERE link_id >= 5;'
);
$installer->getConnection()->query(
    'ALTER TABLE '.$installer->getTableName('mst_seoautolink_link').' AUTO_INCREMENT = 5;'
);

/** @var Mirasvit\SeoAutolink\Model\Link $link */
$link = $objectManager->create('Mirasvit\SeoAutolink\Model\Link');
$link->setKeyword('snow')
    ->setUrl('http://snow.com')
    ->setIsActive(1)
    ->setStoreIds([$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()])
    ->save();

$link = $objectManager->create('Mirasvit\SeoAutolink\Model\Link');
$link->setKeyword('cat')
    ->setUrl('http://cat.com')
    ->setIsActive(1)
    ->setStoreIds([$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()])
    ->save();

$link = $objectManager->create('Mirasvit\SeoAutolink\Model\Link');
$link->setKeyword('mouse')
    ->setUrl('http://mouse.com')
    ->setIsActive(1)
    ->setStoreIds([$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()])
    ->save();

$link = $objectManager->create('Mirasvit\SeoAutolink\Model\Link');
$link->setKeyword('toyboy')
    ->setUrl('http://toyboy.com')
    ->setIsActive(1)
    ->setStoreIds([$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()])
    ->save();

$link = $objectManager->create('Mirasvit\SeoAutolink\Model\Link');
$link->setKeyword('การส')
    ->setUrl('http://fafafa.com')
    ->setIsActive(1)
    ->setStoreIds([$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()])
    ->save();

$link = $objectManager->create('Mirasvit\SeoAutolink\Model\Link');
$link->setKeyword('снегири')
    ->setUrl('http://birds.com')
    ->setIsActive(1)
    ->setStoreIds([$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()])
    ->save();

$link = $objectManager->create('Mirasvit\SeoAutolink\Model\Link');
$link->setKeyword('link1')
    ->setUrl('http://link1.com')
    ->setIsActive(1)
    ->setStoreIds([$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()])
    ->save();

$link = $objectManager->create('Mirasvit\SeoAutolink\Model\Link');
$link->setKeyword('link2')
    ->setUrl('http://link2.com')
    ->setIsActive(1)
    ->setStoreIds([$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()])
    ->save();

$link = $objectManager->create('Mirasvit\SeoAutolink\Model\Link');
$link->setKeyword('спиннинг')
    ->setUrl('http://spinning.com')
    ->setIsActive(1)
    ->setStoreIds([$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()])
    ->save();

$link = $objectManager->create('Mirasvit\SeoAutolink\Model\Link');
$link->setKeyword('spinning')
    ->setUrl('http://spinning.com')
    ->setIsActive(1)
    ->setStoreIds([$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()])
    ->save();

$link = $objectManager->create('Mirasvit\SeoAutolink\Model\Link');
$link->setKeyword('solid')
    ->setUrl('http://solid.com')
    ->setIsActive(1)
    ->setStoreIds([$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()])
    ->save();

$link = $objectManager->create('Mirasvit\SeoAutolink\Model\Link');
$link->setKeyword('ในการล็อกอินเพื่อสมัครสมาชิกสามารทำได้2วิธี')//@codingStandardsIgnoreStart
    ->setUrl('http://thai.com')
    ->setIsActive(1)
    ->setStoreIds([$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()])
    ->save();

$link = $objectManager->create('Mirasvit\SeoAutolink\Model\Link');
$link->setKeyword('123link')
    ->setUrl('http://123link.com')
    ->setIsActive(1)
    ->setStoreIds([$objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()])
    ->save();