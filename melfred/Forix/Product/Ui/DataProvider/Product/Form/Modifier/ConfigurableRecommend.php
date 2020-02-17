<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 11/06/2018
 * Time: 12:50
 */

namespace Forix\Product\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use \Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\ConfigurablePanel;
use Magento\Ui\Component\Form;

class ConfigurableRecommend extends AbstractModifier
{

    /**
     * @var \\Forix\Product\Model\Product\LinkOptions\Recommend
     */
    protected $_recommend;

    public function __construct(
        \Forix\Product\Model\Product\LinkOptions\Recommend $recommend
    )
    {
        $this->_recommend = $recommend;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @param array $metaMatrix
     * @return array
     */
    protected function modifyMatrixMap($metaMatrix)
    {
        $metaMatrix['arguments']['data']['config']['map'] = array_merge($metaMatrix['arguments']['data']['config']['map'], ['recommend_sku' => 'recommend_sku']);
        return $metaMatrix;
    }

    /**
     * @param array $metaMatrix
     * @return array
     */
    protected function modifyMatrixRow($metaMatrix)
    {
        $recommendChild = [
            'recommend_sku_container' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Text::NAME,
                            'formElement' => Form\Element\Select::NAME,
                            'componentType' => Form\Field::NAME,
                            'component' => 'Magento_Ui/js/form/element/select',/*
                            'template' => 'Forix_Product/components/cell-checkbox',*/
                            'label' => __('Is Recommend'),
                            'dataScope' => 'recommend_sku',
                            'options' => $this->_recommend->getAllOptions()
                        ],
                    ],
                ],
            ]
        ];
        $metaMatrix['children']['record']['children'] = array_merge($metaMatrix['children']['record']['children'], $recommendChild);
        return $metaMatrix;
    }

    /**
     * @param array $meta
     * @return array
     * @since 100.1.0
     */
    public function modifyMeta(array $meta)
    {
        if(isset($meta[ConfigurablePanel::GROUP_CONFIGURABLE]['children']['configurable-matrix']['arguments'])) {
            $meta[ConfigurablePanel::GROUP_CONFIGURABLE]['children']['configurable-matrix'] = $this->modifyMatrixMap($meta[ConfigurablePanel::GROUP_CONFIGURABLE]['children']['configurable-matrix']);
            $meta[ConfigurablePanel::GROUP_CONFIGURABLE]['children']['configurable-matrix'] = $this->modifyMatrixRow($meta[ConfigurablePanel::GROUP_CONFIGURABLE]['children']['configurable-matrix']);
        }
        return $meta;
    }
}