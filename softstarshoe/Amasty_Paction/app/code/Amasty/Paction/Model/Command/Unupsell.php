<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;

class Unupsell extends Unrelate
{
    public function __construct(
        ResourceConnection $resource
    ) {
        parent::__construct($resource);

        $this->_type = 'unupsell';
        $this->_info = [
            'confirm_title'   => 'Remove Up-sells',
            'confirm_message' => 'Are you sure you want to remove up-sells?',
            'type'            => $this->_type,
            'label'           => 'Remove Up-sells',
            'fieldLabel'      => 'Select Algorithm'
        ];
    }
}