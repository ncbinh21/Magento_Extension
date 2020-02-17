<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 08/08/2018
 * Time: 15:23
 */

namespace Forix\Product\Ui\DataProvider\Product\Form\Modifier;


use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Product\Attribute\Backend\Sku;
use \Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\ConfigurablePanel as MagentoConfigurablePanel;

class ConfigurablePanel extends MagentoConfigurablePanel
{

    /**
     * @param array $data
     * @return array
     * @since 100.1.0
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     * @since 100.1.0
     */
    public function modifyMeta(array $meta)
    {
        $meta[self::GROUP_CONFIGURABLE]['children']['configurable-matrix']['children']['record']['children']['sku_container'] = array_merge_recursive(
            $meta[self::GROUP_CONFIGURABLE]['children']['configurable-matrix']['children']['record']['children']['sku_container'],
            [
                'validation' =>
                    [
                        'min_text_length' => '1',
                        'max_text_length' => Sku::SKU_MAX_LENGTH,
                    ]
            ]
        );
        return $meta;
    }
}