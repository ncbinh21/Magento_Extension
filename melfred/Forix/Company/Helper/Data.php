<?php
/**
 * Created by PhpStorm.
 * User: hai
 * Date: 28/06/2019
 * Time: 13:53
 */

namespace Forix\Company\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    public $regionFactory;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Directory\Model\RegionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Directory\Model\RegionFactory $regionFactory
    ) {
        parent::__construct($context);
        $this->regionFactory = $regionFactory;
    }

    /**
     * @param $regionId
     * @return string
     */
    public function getRegionDefaultName($regionId)
    {
        return $this->regionFactory->create()->load($regionId)->getDefaultName();
    }
}