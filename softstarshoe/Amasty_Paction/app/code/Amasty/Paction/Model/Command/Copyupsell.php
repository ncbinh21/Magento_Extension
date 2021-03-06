<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;


class Copyupsell extends Copyrelate
{
    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        ResourceConnection $resource
    )
    {
        parent::__construct($helper, $resource);
        $this->_type = 'copyupsell';
        $this->_info = [
            'confirm_title'   => 'Copy Up-sells',
            'confirm_message' => 'Are you sure you want to copy up-sells?',
            'type'            => $this->_type,
            'label'           => 'Copy Up-sells',
            'fieldLabel'      => 'From'
        ];
    }
}