<?php
namespace Forix\AdvancedAttribute\Controller\Adminhtml\Attributes;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Forix\AdvancedAttribute\Model\Image as ImageModel;
use Forix\AdvancedAttribute\Model\Upload;

class Save extends \Forix\AdvancedAttribute\Controller\Adminhtml\Banners
{
    protected $_option;
    protected $imageModel;
    protected $uploadModel;
    protected $_eavAttributeRepository;
    protected $_productCollectionFactory;
    protected $_jsHelper;
    protected $_attributeCode;
    protected $_urlRewriteFactory;
    protected $_storeManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        ImageModel $imageModel,
        Upload $uploadModel,
        \Magento\Eav\Api\AttributeRepositoryInterface $eavAttributeRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->imageModel = $imageModel;
        $this->uploadModel = $uploadModel;
        $this->_eavAttributeRepository = $eavAttributeRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_jsHelper = $jsHelper;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $coreRegistry);

    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $data = $this->getRequest()->getPost('options');
        if ($data) {
            $model = $this->_objectManager->create('Forix\AdvancedAttribute\Model\Option');
            if (isset($data['banner_id']) && (int)$data['banner_id'] > 0) {
                $model->load((int)$data['banner_id']);
            } else {
                $_isExits = $this->checkBeforeSave($data['option_id'], $data['attribute_id']);
                if ($_isExits) {
                    $this->messageManager->addErrorMessage(__('The banner option attribute already exists.'));

                    return $resultRedirect->setPath(
                        '*/*/manage',
                        [
                            'attrid' => $data['attribute_id'], 'attrcode' => $data['attribute_code'],
                            '_current' => true
                        ]);
                }
            }
            $this->saveProducts($data['attribute_code'], $model->getOptionId());

            try {
                $_image = $this->uploadModel->uploadFileAndGetName('image', $this->imageModel->getBaseDir(), $data);
                $_iconNormal = $this->uploadModel->uploadFileAndGetName('icon_normal', $this->imageModel->getBaseDir(), $data);
                $_iconHover = $this->uploadModel->uploadFileAndGetName('icon_hover', $this->imageModel->getBaseDir(), $data);
                $_iconPage = $this->uploadModel->uploadFileAndGetName('icon_page', $this->imageModel->getBaseDir(), $data);
                $model->setData($data)
                    ->setImage($_image)
                    ->setIconNormal($_iconNormal)
                    ->setIconHover($_iconHover)
                    ->setIconPage($_iconPage);
                $model->save();
                
                $this->messageManager->addSuccess(__('The banner has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit',
                        ['id' => $model->getBannerId(), 'attrid' => $data['attribute_id'],
                            'attrcode' => $data['attribute_code'], '_current' => true]);
                }

                return $resultRedirect->setPath(
                    '*/*/manage',
                    [
                        'attrid' => $data['attribute_id'], 'attrcode' => $data['attribute_code'],
                        '_current' => true
                    ]);

            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, $e->getMessage());
            }

        }
        return $resultRedirect->setPath('*/*/edit', ['id' => $data['banner_id'], 'attrid' => $data['attribute_id'], 'attrcode' => $data['attribute_code'], '_current' => true]);
    }

    public function checkBeforeSave($optionId, $attributeId)
    {
        $collection = $this->_objectManager->create('Forix\AdvancedAttribute\Model\ResourceModel\Option\Collection');
        $collection->addFieldToFilter('attribute_id', $attributeId)
            ->addFieldToFilter('option_id', $optionId);
        if ($collection->getSize() > 0)
            return true;
        else
            return false;

    }

    public function saveProducts($attributeCode, $optionValue)
    {
        $productsData = $this->getRequest()->getPost('products');
        if (isset($productsData)) {
            $newProductIds = $this->_jsHelper->decodeGridSerializedInput($productsData);
            $oldProductIds = $this->getProductIdsByOptionValue($attributeCode, $optionValue);
            try {
                $insert = array_diff($newProductIds, $oldProductIds);
                $delete = array_diff($oldProductIds, $newProductIds);
                $totalArray = array_merge($insert, $delete);
                if (empty($totalArray)) {
                    return;
                }
                $collection = $this->_productCollectionFactory->create()
                    ->addFieldToFilter('entity_id', array('in' => $totalArray))
                    ->addFieldToSelect($attributeCode);
                if ($collection->getSize()) {
                    $attribute = $this->getProductAttributeByCode($attributeCode);
                    if (!$attribute->getId()) {
                        return;
                    }
                    foreach ($collection as $product) {
                        if ($attribute->getFrontendInput() == 'multiselect') {
                            $currentValues = $product->getData($attributeCode);
                            if (in_array($product->getId(), $insert)) {
                                if (empty($currentValues)) {
                                    $product->setData($attributeCode, $optionValue);
                                } else {
                                    $product->setData($attributeCode, $currentValues . ',' . $optionValue);
                                }
                            } else {
                                $currentValues = explode(',', $currentValues);
                                $currentValues = array_diff($currentValues, array($optionValue));
                                $product->setData($attributeCode, implode(',', $currentValues));
                            }
                        } else {
                            if (in_array($product->getId(), $insert)) {
                                $product->setData($attributeCode, $optionValue);
                            } else {
                                $product->setData($attributeCode, null);
                            }
                        }
                        $product->getResource()->saveAttribute($product, $attributeCode);
                    }
                }
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the banner option.'));
            }
        }

        return;
    }

    protected function getProductIdsByOptionValue($attributeCode, $optionValue)
    {
        $result = array();
        $attribute = $this->getProductAttributeByCode($attributeCode);
        if (!$attribute->getId()) {
            return $result;
        }
        $collection = $this->_productCollectionFactory->create();

        if ($attribute->getFrontendInput() == 'multiselect') {
            $result = $collection->addFieldToFilter($attributeCode, array('finset' => $optionValue))
                ->getAllIds();
        } else {
            $result = $collection->addFieldToFilter($attributeCode, array('in' => $optionValue))
                ->getAllIds();
        }

        return $result;
    }

    protected function getProductAttributeByCode($attributeCode)
    {
        if (is_null($this->_attributeCode)) {
            $this->_attributeCode = $this->_eavAttributeRepository->get(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE, $attributeCode);
        }

        return $this->_attributeCode;
    }

}