<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 16/11/2018
 * Time: 13:39
 */

namespace Forix\Payment\Model\Service\Sage100;


class PaymentType implements \Magento\Framework\Data\OptionSourceInterface
{
    const CREDIT_CARD = 'CREDIT CARD';
    const CASH = 'CASH';
    const NONE = 'NONE';
    const CHECK = 'CHECK';

    /**
     * @var array
     */
    protected $options;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $types = $this->getAvailablePaymentTypes();
        foreach ($types as $typeCode => $typeName) {
            $this->options[$typeCode]['label'] = $typeName;
            $this->options[$typeCode]['value'] = $typeCode;
        }

        return $this->options;
    }


    /**
     * @return array
     */
    private function getAvailablePaymentTypes()
    {
        // @codingStandardsIgnoreStart
        return [
            self::CREDIT_CARD => __("Credit Card"),
            self::CASH => __("Cash"),
            self::NONE => __("None"),
            self::CHECK => __("Check")
        ];
        // @codingStandardsIgnoreEnd
    }
}