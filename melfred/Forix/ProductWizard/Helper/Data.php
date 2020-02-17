<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 29/08/2018
 * Time: 14:03
 */

namespace Forix\ProductWizard\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_attributeRepository;
    protected $_groupItemCollectionFactory;

    public function __construct(
        Context $context,
        \Forix\ProductWizard\Model\ResourceModel\GroupItem\CollectionFactory $groupItemCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository
    )
    {
        $this->_groupItemCollectionFactory = $groupItemCollectionFactory;
        $this->_attributeRepository = $attributeRepository;
        parent::__construct($context);
    }

    public function synchronize()
    {
        $differences = $this->getDifferenceOptions();
        foreach ($differences as $groupItemId => $difference) {
            $add = $difference['add'];
            $remove = $difference['remove'];
        }
    }

    public static function cleanCssIdentifier($identifier, array $filter = array(
        ' ' => '',
        '_' => '',
        '/' => '',
        '[' => '',
        ']' => '',
    )) {

        // We could also use strtr() here but its much slower than str_replace(). In
        // order to keep '__' to stay '__' we first replace it with a different
        // placeholder after checking that it is not defined as a filter.
        $double_underscore_replacements = 0;
        if (!isset($filter['__'])) {
            $identifier = str_replace('__', '##', $identifier, $double_underscore_replacements);
        }
        $identifier = str_replace(array_keys($filter), array_values($filter), $identifier);

        // Replace temporary placeholder '##' with '__' only if the original
        // $identifier contained '__'.
        if ($double_underscore_replacements > 0) {
            $identifier = str_replace('##', '__', $identifier);
        }

        // Valid characters in a CSS identifier are:
        // - the hyphen (U+002D)
        // - a-z (U+0030 - U+0039)
        // - A-Z (U+0041 - U+005A)
        // - the underscore (U+005F)
        // - 0-9 (U+0061 - U+007A)
        // - ISO 10646 characters U+00A1 and higher
        // We strip out any character not in the above list.
        $identifier = preg_replace('/[^\\x{002D}\\x{0030}-\\x{0039}\\x{0041}-\\x{005A}\\x{005F}\\x{0061}-\\x{007A}\\x{00A1}-\\x{FFFF}]/u', '', $identifier);

        // Identifiers cannot start with a digit, two hyphens, or a hyphen followed by a digit.
        $identifier = preg_replace(array(
            '/^[0-9]/',
            '/^(-[0-9])|^(--)/',
        ), array(
            '_',
            '__',
        ), $identifier);
        return strtolower($identifier);
    }
    /**
     * @param $groupItemId
     * @param $options
     */
    public function saveOptions($groupItemId, $options)
    {

    }

    /**
     * @param $groupItemId
     * @param $options
     */
    public function deleteOptions($groupItemId, $options)
    {

    }


    /**
     * @param $attrCode
     */
    public function getDifferenceOptions()
    {
        $groupItemCollection = $this->_groupItemCollectionFactory->create();
        $groupItemCollection->addFieldToFilter('auto_add_item_option', 1);
        $results = [];
        $attrOptions = [];
        /**
         * @var $groupItem \Forix\ProductWizard\Model\GroupItem
         */
        foreach ($groupItemCollection as $groupItem) {
            if (!isset($results[$groupItem->getId()])) {
                $results[$groupItem->getId()] = [
                    'add' => [],
                    'remove' => []
                ];
            }
            if (isset($attrOptions[$groupItem->getAttributeCode()])) {
                $attrOptions[$groupItem->getAttributeCode()] = $this->getCurrentAttributeValues($groupItem->getAttributeCode());
            }
            $groupItemOptions = $groupItem->getOptionCollection()->toArray(['group_item_option_id', 'option_value']);

            foreach ($attrOptions[$groupItem->getAttributeCode()] as $attrOption) {
                $has = false;
                foreach ($groupItemOptions['items'] as $key => $groupItemOption) {
                    if ($attrOption->getValue() == $groupItemOption['option_value']) {
                        $has = true;
                        unset($groupItemOptions['items'][$key]);
                        break;
                    }
                }
                if (!$has) {
                    $results[$groupItem->getId()]['add'][] = $attrOption;
                }
            }
            $results[$groupItem->getId()]['remove'] = array_merge($results[$groupItem->getId()]['remove'], $groupItemOptions['items']);
        }
        return $results;
    }

    /**
     * @param $attrCode
     * @return array|\Magento\Eav\Api\Data\AttributeOptionInterface|\Magento\Eav\Model\Entity\Attribute\Option|\Magento\Eav\Model\Entity\Attribute\Option[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCurrentAttributeValues($attrCode)
    {
        /**
         * @var \Magento\Eav\Model\Entity\Attribute\Option[] $attributeOptions
         * @var \Magento\Eav\Model\Entity\Attribute\Option[] $attributeValues
         */
        $attributeOptions = $this->_attributeRepository->get($attrCode)->getOptions();
        $attributeValues = [];
        foreach ($attributeOptions as $option) {
            if ($option->getValue()) {
                $attributeValues = $option;
            }
        }
        return $attributeValues;
    }

    /**
     * Return attribute code of attribute set from config
     * @param $attributeSetId
     * @param $storeId
     * @return array
     */
    public function getConfigAttributes($attributeSetId, $storeId = null)
    {
        $attributes = $this->scopeConfig->getValue('wizard/general/attributes');
        $attributes = str_replace('mb_rig_model', '', $attributes);
        $attributes = explode(',', $attributes);
        $attributes = array_filter($attributes);
        return $attributes;
    }
}