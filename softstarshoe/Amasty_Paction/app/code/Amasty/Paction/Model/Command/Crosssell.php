<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;


class Crosssell extends Relate
{
    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        ResourceConnection $resource
    )
    {
        parent::__construct($helper, $resource);
        $this->_type = 'crosssell';

        $this->_info = [
            'confirm_title'   => 'Cross-sell',
            'confirm_message' => 'Are you sure you want to cross-sell?',
            'type'            => $this->_type,
            'label'           => 'Cross-sell',
            'fieldLabel'      => 'Selected To IDs',
            'placeholder'     => 'id1,id2,id3'
        ];
        $this->setFieldLabel();
    }
}
