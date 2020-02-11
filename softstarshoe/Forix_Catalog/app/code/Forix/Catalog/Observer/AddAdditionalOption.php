<?php
namespace Forix\Catalog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AddAdditionalOption implements ObserverInterface
{
    const LIST_ATTRIBUTE_CLEARANCE = 'clearance/clearance_configuration/clearance_attribute';
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     *
     */
    protected $scopeConfig;

    /**
     * AddAdditionalOption constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Json $serializer
     * @param RequestInterface $request
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        Json $serializer,
        RequestInterface $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->serializer = $serializer;
        $this->_request = $request;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_request->getFullActionName() == 'checkout_cart_add') {
            $additionalOptions = [];
            $product = $observer->getProduct();
            $isClearance = $this->isClearance($product);
            if($product->getTypeId() == 'simple' && $isClearance) {
                $listAttributes = $this->scopeConfig->getValue(self::LIST_ATTRIBUTE_CLEARANCE);
                $arrListAttributes = explode("\n", $listAttributes);
                foreach ($arrListAttributes as $attribute) {
                    $attribute = trim(strtr($attribute , array("\r" => "")));
                    if($product->getResource()->getAttribute($attribute)) {
                        $optionTitle = $product->getResource()->getAttribute($attribute)->getFrontendLabel();
                        if(!($optionText = $product->getResource()->getAttribute($attribute)->getSource()->getOptionText($product->getData($attribute)))) {
                            $optionText = $product->getData($attribute);
                        }
                        if($optionTitle && $optionText){
                            $additionalOptions[] = array(
                                'label' => $optionTitle,
                                'value' => $optionText,
                            );
                        }
                    }
                }
                $observer->getProduct()->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
            }
        }
    }

    /**
     * @param $product
     * @return bool
     */
    public function isClearance($product)
    {
        $categoryIds = $product->getCategoryIds();
        if(isset($categoryIds) && is_array($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                $category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
                if($category->getName() == 'Clearance' || $category->getParentCategory()->getName() == 'Clearance') {
                    return true;
                }
            }
        }
        return false;
    }
}