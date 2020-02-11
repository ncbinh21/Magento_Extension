<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Source\Giftcard;

use Magento\Framework\Option\ArrayInterface;
use Magento\Config\Model\Config\Source\Email\Template as SourceEmailTemplate;

/**
 * Class EmailTemplate
 *
 * @package Aheadworks\Giftcard\Model\Source\Giftcard
 */
class EmailTemplate implements ArrayInterface
{
    /**
     * 'Do not send' option value
     */
    const DO_NOT_SEND = '0';

    /**
     * @var SourceEmailTemplate
     */
    private $emailTemplates;

    /**
     * @param SourceEmailTemplate $emailTemplates
     */
    public function __construct(
        SourceEmailTemplate $emailTemplates
    ) {
        $this->emailTemplates = $emailTemplates;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $optionArray = $this->emailTemplates
            ->setPath('aw_giftcard_email_template')
            ->toOptionArray();

        array_unshift(
            $optionArray,
            [
                'value' => self::DO_NOT_SEND,
                'label' => __('Do not send')
            ]
        );
        return $optionArray;
    }
}
