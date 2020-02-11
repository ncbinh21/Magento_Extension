<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Plugin\Ui\Model;

class Reader extends AbstractReader
{
    /**
     * Create xml config with php for enable\disable it from admin panel
     *
     * @param \Magento\Ui\Config\Reader $subject
     * @param array $result
     * @param array $arguments
     * @return array
     */
    public function afterRead(
        \Magento\Ui\Config\Reader $subject,
        $result
    ) {
        $availableActions = $this->helper->getModuleConfig('general/commands');
        $availableActions = explode(',', $availableActions);

        if ($this->moduleManager->isOutputEnabled('Amasty_Paction') &&
            isset($result['children']['listing_top']['children']['listing_massaction']['children']) &&
            isset($result['children']['product_listing_data_source'])
        ) {
            $children = &$result['children']['listing_top']['children']['listing_massaction']['children'];
            foreach($availableActions as $item) {
                if (array_key_exists($item, $children)) {
                    continue;
                }
                $children[$item] = $this->generateElement($item);
            }
        }
        return $result;
    }
}
