<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Config\Source;

use Amasty\Fpc\Helper\Http as HttpHelper;

class ResponseCode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var HttpHelper
     */
    private $httpHelper;

    public function __construct(
        HttpHelper $httpHelper
    ) {
        $this->httpHelper = $httpHelper;
    }

    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Already cached'),
                'value' => HttpHelper::STATUS_ALREADY_CACHED
            ]
        ];

        $codes = $this->httpHelper->getStatusCodes();

        foreach ($codes as $code => $description) {
            if ($code == HttpHelper::STATUS_ALREADY_CACHED) {
                continue;
            }

            $options []= [
                'label' => "$code $description",
                'value' => $code
            ];
        }

        return $options;
    }
}
