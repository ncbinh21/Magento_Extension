<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 31/07/2018
 * Time: 16:43
 */

namespace Forix\Product\Block\Plugin\Product\View\Type;


class Configurable
{
    protected $_jsonEncoder;
    protected $_registry;

    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $jsonEncoder,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_registry = $registry;
    }

    /**
     * @param $subject \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
     * @param $html
     * @return string
     */
    public function afterToHtml($subject, $html)
    {
        if (false !== strpos($subject->getNameInLayout(), 'product.info.options.')) {
            try {
                $aStockStatus = $this->_registry->registry('forix_stock_message_data');
                $aStockStatus['changeConfigurableStatus'] = true;
                $data = $this->_jsonEncoder->serialize($aStockStatus);

                $html = '<script type="text/x-magento-init">
                    {
                        ".product-options-wrapper": {
                                    "stockMessages": {
                                        "config": ' . $data . '
                                    }
                         }
                    }
                   </script>' . $html;
            }catch (\InvalidArgumentException $e){
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $objectManager->get('Psr\Log\LoggerInterface')->critical($e);

            }

        }
        return $html;
    }
}