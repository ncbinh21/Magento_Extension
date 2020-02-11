<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Plugin\Ui\Model;

class Manager extends AbstractReader
{
    /**
     * Create xml config with php for enable\disable it from admin panel
     *
     * @param \Magento\Ui\Model\Manager $subject
     * @param array $result
     * @param array $arguments
     * @return array
     */
    public function afterGetData(
        \Magento\Ui\Model\Manager $subject,
        $result
    ) {
        $availableActions = $this->helper->getModuleConfig('general/commands');
        $availableActions = explode(',', $availableActions);

        if ($this->moduleManager->isOutputEnabled('Amasty_Paction') &&
            isset($result['product_listing']['children']['listing_top']['children']['listing_massaction']['children'])
        ) {
            $children = &$result['product_listing']['children']['listing_top']['children']['listing_massaction']['children'];
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
