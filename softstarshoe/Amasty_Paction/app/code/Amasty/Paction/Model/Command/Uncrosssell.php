<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;

class Uncrosssell extends Unrelate
{
    public function __construct(
        ResourceConnection $resource
    ) {
        parent::__construct($resource);

        $this->_type = 'uncrosssell';
        $this->_info = [
            'confirm_title'   => 'Remove Cross-Sells',
            'confirm_message' => 'Are you sure you want to remove cross-Sells?',
            'type'            => $this->_type,
            'label'           => 'Remove Cross-Sells',
            'fieldLabel'      => 'Select Algorithm'
        ];
    }
}