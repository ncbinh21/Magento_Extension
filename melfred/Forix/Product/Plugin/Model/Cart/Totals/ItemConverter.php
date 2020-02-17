<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */
namespace Forix\Product\Plugin\Model\Cart\Totals;

class ItemConverter
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * AbstractItems constructor.
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ){
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    public function afterModelToDataObject(\Magento\Quote\Model\Cart\Totals\ItemConverter $subject, $result)
    {
        if($result->getOptions()) {
            $options = $this->serializer->unserialize($result->getOptions());
            $rigModel = [];
            if (isset($options) && !empty($options)){
                foreach ($options as $key => $value) {
                    if ($value['label'] == __("Your Rig Model")){
                        $rigModel = $options[$key];
                        unset($options[$key]);
                        break;
                    }
                }
            }
            if($rigModel) {
                array_unshift($options, $rigModel);
            }
            $result->setOptions($this->serializer->serialize($options));
        }
        return $result;
    }
}


