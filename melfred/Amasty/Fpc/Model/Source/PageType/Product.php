<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

class Product extends Rewrite
{
    protected $rewriteType = \Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite::ENTITY_TYPE_PRODUCT;
}
