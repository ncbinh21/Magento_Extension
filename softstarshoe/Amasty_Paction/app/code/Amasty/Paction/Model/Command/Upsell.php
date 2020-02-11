<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;


class Upsell extends Relate
{
    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        ResourceConnection $resource
    )
    {
        parent::__construct($helper, $resource);
        $this->_type = 'upsell';
        $this->_info = [
            'confirm_title'   => 'Up-sell',
            'confirm_message' => 'Are you sure you want to up-sell?',
            'type'            => $this->_type,
            'label'           => 'Up-sell',
            'placeholder'     => 'id1,id2,id3',
            'fieldLabel'      => ''
        ];

        $this->setFieldLabel();
    }
}
