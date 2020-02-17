<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */
namespace Forix\InternationalOrders\Plugin\Minicart;

class ItemPlugin
{
    /**
     * @var \Forix\InternationalOrders\Helper\Helper
     */
    protected $helper;

    /**
     * ItemPlugin constructor.
     * @param \Forix\InternationalOrders\Helper\Helper $helper
     */
    public function __construct(
        \Forix\InternationalOrders\Helper\Helper $helper
    ) {
        $this->helper = $helper;
    }

    public function afterGetItemData(\Magento\Checkout\CustomerData\AbstractItem $subject, $result)
    {
        $result = \array_merge(
            [
                'is_domestic' => $this->helper->isDomestic()
            ],
            $result
        );
        return $result;
    }
}