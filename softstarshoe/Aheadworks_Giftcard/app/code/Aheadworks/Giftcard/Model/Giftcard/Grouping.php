<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Giftcard;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Magento\Framework\Api\SimpleDataObjectConverter;

/**
 * Class Grouping
 *
 * @package Aheadworks\Giftcard\Model\Giftcard
 */
class Grouping
{
    /**
     * @var SimpleDataObjectConverter
     */
    private $simpleDataObjectConverter;

    /**
     * @var string[]
     */
    private $groupByFields;

    /**
     * @var []
     */
    private $giftcardObjectMethods;

    /**
     * @var []
     */
    private $giftcardExtensionObjectMethods;

    /**
     * @var int
     */
    private $countFields = 0;

    /**
     * @var int
     */
    private $counter = 0;

    /**
     * @var GiftcardInterface[]
     */
    private $giftcardsGrouped = [];

    /**
     * @var []
     */
    private $groupedFilters = [];

    /**
     * @param SimpleDataObjectConverter $simpleDataObjectConverter
     * @param string[] $groupByFields
     */
    public function __construct(
        SimpleDataObjectConverter $simpleDataObjectConverter,
        $groupByFields = []
    ) {
        $this->simpleDataObjectConverter = $simpleDataObjectConverter;
        $this->groupByFields = $groupByFields;
        $this->countFields = count($this->groupByFields);
    }

    /**
     * Process grouping
     *
     * @param GiftcardInterface[] $giftcards
     * @return GiftcardInterface[]
     */
    public function process($giftcards)
    {
        $this->reset();
        foreach ($giftcards as $giftcard) {
            $newGroup = [];
            foreach ($this->groupByFields as $field) {
                if ($methodName = $this->getMethodByFieldName($giftcard, $field)) {
                    $newGroup[$field] = $giftcard->{$methodName}();
                } elseif ($methodName = $this->getMethodByFieldName($giftcard, $field, true)) {
                    $newGroup[$field] = $giftcard->getExtensionAttributes()->{$methodName}();
                }
            }
            $matchedFields = 0;
            $groupAlias = '';
            foreach ($this->groupedFilters as $groupKey => $group) {
                $matchedFields = 0;
                $groupAlias = $groupKey;
                foreach ($group as $key => $value) {
                    if (isset($newGroup[$key]) && $value == $newGroup[$key]) {
                        $matchedFields++;
                    }
                }
            }
            if ($this->countFields != $matchedFields) {
                $groupAlias = 'group-' . $this->getNextIncrement();
                $this->groupedFilters[$groupAlias] = $newGroup;
            }
            $this->giftcardsGrouped[$groupAlias][] = $giftcard;
        }
        return $this->giftcardsGrouped;
    }

    /**
     * Reset fields
     *
     * @return void
     */
    private function reset()
    {
        $this->counter = 0;
        $this->giftcardsGrouped = [];
        $this->groupedFilters = [];
    }

    /**
     * Retrieve method by field name
     *
     * @param GiftcardInterface $giftcard
     * @param string $field
     * @param bool $searchInExtensionAttributes
     * @return string
     */
    private function getMethodByFieldName($giftcard, $field, $searchInExtensionAttributes = false)
    {
        if (null === $this->giftcardObjectMethods) {
            $this->giftcardObjectMethods = get_class_methods(get_class($giftcard));
        }
        if (null === $this->giftcardExtensionObjectMethods) {
            $this->giftcardExtensionObjectMethods = [];
            if ($giftcard->getExtensionAttributes()) {
                $this->giftcardExtensionObjectMethods = get_class_methods(
                    get_class($giftcard->getExtensionAttributes())
                );
            }
        }
        $camelCaseField = $this->simpleDataObjectConverter->snakeCaseToUpperCamelCase($field);
        $possibleMethods = [
            'get' . $camelCaseField,
            'is' . $camelCaseField,
        ];
        $giftcardMethods = $searchInExtensionAttributes
            ? $this->giftcardExtensionObjectMethods
            : $this->giftcardObjectMethods;

        $methodNames = array_intersect($possibleMethods, $giftcardMethods);
        $methodName = array_shift($methodNames);

        return $methodName;
    }

    /**
     * Increment and return counter
     *
     * @return int
     */
    private function getNextIncrement()
    {
        return ++$this->counter;
    }
}
