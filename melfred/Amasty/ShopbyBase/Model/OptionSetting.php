<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Model;

use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Amasty\ShopbyBase\Api\Data\OptionSettingRepositoryInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Model\Product\Attribute\Repository as AttributeRepository;

/**
 * Class OptionSetting
 * @method \Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\Collection getCollection()
 * @package Amasty\ShopbyBase\Model
 */
class OptionSetting extends \Magento\Framework\Model\AbstractModel implements OptionSettingInterface, IdentityInterface
{
    const CACHE_TAG = 'amshopby_option_setting';
    const IMAGES_DIR = '/amasty/shopby/option_images/';
    const SLIDER_DIR = 'slider/';

    protected $_eventPrefix = 'amshopby_option_setting';

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var  Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     * @var \Magento\Framework\Url
     */
    protected $url;

    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var OptionSettingRepositoryInterface
     */
    private $optionSettingRepository;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Filesystem $fileSystem,
        Filesystem\Driver\File $file,
        UploaderFactory $uploaderFactory,
        \Magento\Framework\Url $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        AttributeRepository $attributeRepository,
        OptionSettingRepositoryInterface $optionSettingRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->fileDriver = $file;
        $this->attributeRepository = $attributeRepository;
        $this->optionSettingRepository = $optionSettingRepository;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->url = $url;
    }

    /**
     * Protected OptionSetting constructor
     */
    protected function _construct()
    {
        $this->_init(\Amasty\ShopbyBase\Model\ResourceModel\OptionSetting::class);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->getData(self::SHORT_DESCRIPTION);
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->getData(self::META_TITLE);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::OPTION_SETTING_ID);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @return bool
     */
    public function getIsFeatured()
    {
        return (bool) $this->getData(self::IS_FEATURED);
    }

    /**
     * @return string
     */
    public function getFilterCode()
    {
        return $this->getData(self::FILTER_CODE);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @return string
     */
    public function getTopCmsBlockId()
    {
        return $this->getData(self::TOP_CMS_BLOCK_ID);
    }

    /**
     * @return string
     */
    public function getSliderPosition()
    {
        return $this->getData(self::SLIDER_POSITION);
    }

    /**
     * @param string $description
     * @return OptionSetting
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @param string $metaDescription
     * @return OptionSetting
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * @param string $metaKeywords
     * @return OptionSetting
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    /**
     * @param string $metaTitle
     * @return OptionSetting
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    /**
     * @param int|mixed $id
     * @return OptionSetting
     */
    public function setId($id)
    {
        return $this->setData(self::OPTION_SETTING_ID, $id);
    }

    /**
     * @param int $id
     * @return OptionSetting
     */
    public function setStoreId($id)
    {
        return $this->setData(self::STORE_ID, $id);
    }

    /**
     * @param int $isFeatured
     * @return OptionSetting
     */
    public function setIsFeatured($isFeatured)
    {
        return $this->setData(self::IS_FEATURED, $isFeatured);
    }

    /**
     * @param string $image
     * @return OptionSetting
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @param string $image
     * @return OptionSetting
     */
    public function setSliderImage($image)
    {
        return $this->setData(self::SLIDER_IMAGE, $image);
    }

    /**
     * @param string $filterCode
     * @return OptionSetting
     */
    public function setFilterCode($filterCode)
    {
        return $this->setData(self::FILTER_CODE, $filterCode);
    }

    /**
     * @param int $value
     * @return OptionSetting
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * @param string $title
     * @return OptionSetting
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @param int|null $id
     * @return OptionSetting
     */
    public function setTopCmsBlockId($id)
    {
        return $this->setData(self::TOP_CMS_BLOCK_ID, $id);
    }

    /**
     * @param int $pos
     * @return OptionSetting
     */
    public function setSliderPosition($pos)
    {
        return $this->setData(self::SLIDER_POSITION, $pos);
    }

    /**
     * @return string
     */
    protected function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @return string
     */
    protected function getSliderImage()
    {
        return $this->getData(self::SLIDER_IMAGE);
    }

    /**
     * @param string|array  $key
     * @param mixed         $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if ($key == self::SLIDER_POSITION) {
            $value = max(0, intval($value));
        }
        return parent::setData($key, $value);
    }

    /**
     * @param int $fileId
     * @param bool $isSlider
     * @return string
     */
    public function uploadImage($fileId, $isSlider = false)
    {
        $mediaDir = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setFilesDispersion(false);
        $uploader->setFilenamesCaseSensitivity(false);
        $uploader->setAllowRenameFiles(true);
        $uploader->setAllowedExtensions(['jpg', 'png', 'jpeg', 'gif', 'bmp', 'svg']);
        $path = $isSlider ? self::IMAGES_DIR . self::SLIDER_DIR : self::IMAGES_DIR;
        $uploader->save($mediaDir->getAbsolutePath($path));
        $result = $uploader->getUploadedFileName();
        $this->removeImage($isSlider);
        return $result;
    }

    /**
     * @param bool $isSlider
     * @return void
     */
    public function removeImage($isSlider = false)
    {
        $useDefault = $isSlider
            ? $this->getData('slider_image_use_default')
            : $this->getData('image_use_default');
        if (!$useDefault || $this->getStoreId() == 0) {
            $img = $isSlider ? $this->getSliderImage() : $this->getImage();
            if ($img) {
                $path = $this->getImagePath($isSlider);
                if ($this->fileDriver->isExists($path)) {
                    $this->fileDriver->deleteFile($path);
                }
            }
        }
    }

    /**
     * @param bool $isSlider
     * @return string
     */
    public function getImagePath($isSlider = false)
    {
        $mediaDir = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $imgPath = $isSlider
            ? self::IMAGES_DIR . self::SLIDER_DIR . $this->getSliderImage()
            : self::IMAGES_DIR . $this->getImage();
        return $mediaDir->getAbsolutePath($imgPath);
    }

    /**
     * @return null|string
     */
    public function getImageUrl()
    {
        if (!$this->getImage()) {
            return null;
        }
        $url = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            . self::IMAGES_DIR . $this->getImage();

        return $url;
    }

    /**
     * @param bool $strict
     * @return null|string
     */
    public function getSliderImageUrl($strict = false)
    {
        if (!$this->getSliderImage()) {
            return $strict
                ? null
                : $this->getImageUrl();
        }
        $url = self::IMAGES_DIR . self::SLIDER_DIR . $this->getSliderImage() ;

        $url = rtrim($this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA), '/')
            . $url; //IMAGES_DIR already has delimiter

        return $url;
    }

    /**
     * Wrapper for repository method
     *
     * @param string $filterCode
     * @param int $optionId
     * @param int $storeId
     * @return OptionSettingInterface
     */
    public function getByParams($filterCode, $optionId, $storeId)
    {
        return $this->optionSettingRepository->getByParams($filterCode, $optionId, $storeId);
    }

    /**
     * @return string
     */
    public function getUrlPath()
    {
        $fCode = $this->getFilterCode();
        if (!$fCode) {
            return $this->url->getBaseUrl();
        }
        $brandCode = str_replace(\Amasty\ShopbyBase\Helper\FilterSetting::ATTR_PREFIX, '', $fCode);
        return $settingUrl = $this->url->getUrl('amshopby/index/index', [
            '_query' => [$brandCode => $this->getValue()],
        ]);
    }

    /**
     * @param string $filterCode
     * @param int $optionId
     * @param int $storeId
     * @param array $data
     * @return OptionSettingInterface|\Magento\Framework\DataObject
     */
    public function saveData($filterCode, $optionId, $storeId, $data)
    {
        $model = $this->getByParams($filterCode, $optionId, $storeId);
        if (!$model->getId()) {
            $model
                ->setValue($optionId)
                ->setFilterCode($filterCode)
                ->setStoreId($storeId);
        } elseif ($model->getStoreId() != $storeId) {
            $model->setId(null);
            $model->isObjectNew(true);
            $model->setStoreId($storeId);
        }

        $defaultModel = $model->getByParams($filterCode, $optionId, 0);
        $this->_processImages($model, $defaultModel, $data);

        if (isset($data['use_default']) && count($data['use_default']) > 0) {
            foreach ($data['use_default'] as $field) {
                $data[$field] = in_array($field, ['meta_title', 'title'])
                    ? ''
                    : $defaultModel->getData($field);
            }
        }

        $model->addData($data);
        $this->optionSettingRepository->save($model);

        return $model;
    }

    /**
     * Save image & slider_image
     *
     * @param \Amasty\ShopbyBase\Api\Data\OptionSettingInterface $model
     * @param \Amasty\ShopbyBase\Api\Data\OptionSettingInterface $defaultModel
     * @param array $data
     * @param bool $isSlider
     * @return OptionSetting|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _processImages($model, $defaultModel, &$data, $isSlider = false)
    {
        $field = $isSlider ? 'slider_image' : 'image';

        $useDefaultImage = false;
        if (isset($data['use_default'])) {
            if (in_array($field, $data['use_default'])) {
                $useDefaultImage = true;
            }
        }

        if ($useDefaultImage && ($model->getData($field) != $defaultModel->getData($field))
                || isset($data[$field . '_delete'])) {
            $model->removeImage($isSlider);
            $data[$field] = '';
        }

        if (!$useDefaultImage) {
            try {
                $imageName = $model->uploadImage($field, $isSlider);
                $data[$field] = $imageName;
            } catch (\Exception $e) {
                if ($e->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY &&
                    $e->getMessage() != '$_FILES array is empty'
                ) {
                    throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
                }
            }
        }
        return $isSlider ? $this
            : $this->_processImages($model, $defaultModel, $data, true);
    }

    /**
     * Get attribute option by current option setting
     *
     * @return \Magento\Eav\Api\Data\AttributeOptionInterface|null
     */
    public function getAttributeOption()
    {
        if (!$this->getData('attribute_option')) {
            $attributeCode = str_replace(
                \Amasty\ShopbyBase\Helper\FilterSetting::ATTR_PREFIX,
                '',
                $this->getFilterCode()
            );
            $options = $this->attributeRepository->get($attributeCode)->getOptions();
            foreach ($options as $option) {
                if ($option->getValue() == $this->getValue()) {
                    $this->setData('attribute_option', $option);
                    break;
                }
            }
        }
        return $this->getData('attribute_option');
    }
}
