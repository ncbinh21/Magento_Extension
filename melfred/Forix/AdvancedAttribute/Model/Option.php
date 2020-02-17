<?php
/**
 * Forix_BannerAttribute extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 * @category  Forix
 * @package   Forix_BannerAttribute
 * @copyright Copyright (c) 2016
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Forix\AdvancedAttribute\Model;

use Magento\Framework\Model\AbstractModel;

class Option extends AbstractModel
{
    protected $_imageModel;
    protected $_uploadModel;
    protected $_urlRewrite;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Forix\AdvancedAttribute\Model\Image $imageModel,
        \Forix\AdvancedAttribute\Model\Upload $uploadModel,
        \Magento\UrlRewrite\Model\UrlRewrite $urlRewrite,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_imageModel = $imageModel;
        $this->_uploadModel = $uploadModel;
        $this->_urlRewrite = $urlRewrite;
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Forix\AdvancedAttribute\Model\ResourceModel\Option');
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    public function beforeDelete()
    {
        if ($this->getData('image')) {
            $this->_uploadModel->removeImage($this->_imageModel->getBaseDir() . $this->getData('image'));
        }
        if ($this->getData('icon_normal')) {
            $this->_uploadModel->removeImage($this->_imageModel->getBaseDir() . $this->getData('icon_normal'));
        }
        if ($this->getData('icon_hover')) {
            $this->_uploadModel->removeImage($this->_imageModel->getBaseDir() . $this->getData('icon_hover'));
        }
        if ($this->getData('logo_attribute')) {
            $this->_uploadModel->removeImage($this->_imageModel->getBaseDir() . $this->getData('logo_attribute'));
        }
        if ($this->getData('icon_page')) {
            $this->_uploadModel->removeImage($this->_imageModel->getBaseDir() . $this->getData('icon_page'));
        }
        return parent::beforeDelete();
    }
}