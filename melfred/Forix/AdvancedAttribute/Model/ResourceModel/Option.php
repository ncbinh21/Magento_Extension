<?php
/**
 * Forix_BannerAttribute extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 *                     @category  Forix
 *                     @package   Forix_BannerAttribute
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Forix\AdvancedAttribute\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Option extends AbstractDb
{
    /**
     * Date model
     *
     * @var DateTime
     */
    protected $date;

    /**
     * constructor
     *
     * @param DateTime $date
     * @param Context $context
     */
    public function __construct(
        DateTime $date,
        Context $context
    )
    {
        $this->date = $date;
        parent::__construct($context);
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_attribute_option_banner', 'banner_id');
    }
}