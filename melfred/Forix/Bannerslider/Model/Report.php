<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\Bannerslider\Model;

/**
 * Report Model
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Report extends \Magento\Framework\Model\AbstractModel
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Forix\Bannerslider\Model\ResourceModel\Report $resource,
        \Forix\Bannerslider\Model\ResourceModel\Report\Collection $resourceCollection
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }
}
