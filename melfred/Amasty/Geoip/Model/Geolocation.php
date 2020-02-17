<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Geoip
 */


namespace Amasty\Geoip\Model;

class Geolocation extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Amasty\Geoip\Helper\Data
     */
    public $geoipHelper;

    /**
     * Geolocation constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Amasty\Geoip\Helper\Data $geoipHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Amasty\Geoip\Helper\Data $geoipHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){

        $this->geoipHelper = $geoipHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * load location data by IP
     *
     * @param string $ip
     *
     * @return $this
     */
    public function locate($ip)
    {
//        $ip = '213.184.226.82';//Minsk
        $ip = substr($ip, 0, strrpos($ip, ".")) . '.0'; // Mask IP according to EU GDPR law

        if ($this->geoipHelper->isDone(false)) {
            $longIP = sprintf("%u", ip2long($ip));

            if (!empty($longIP)) {
                $connection =  $this->_resource->getConnection('read');
                $blockSelect = $connection->select()
                    ->from($this->_resource->getTableName('amasty_geoip_block'))
                    ->reset(\Magento\Framework\DB\Select::COLUMNS)
                    ->columns(['geoip_loc_id'])
                    ->where('start_ip_num < ?', $longIP)
                    ->order('start_ip_num DESC')
                    ->limit(1);

                $select = $connection->select()
                    ->from(['b' => $blockSelect])
                    ->joinInner(
                        ['l' => $this->_resource->getTableName('amasty_geoip_location')],
                        'l.geoip_loc_id = b.geoip_loc_id',
                        null
                    )
                    ->reset(\Magento\Framework\DB\Select::COLUMNS)
                    ->columns(['l.*']);

                if ($result = $connection->fetchRow($select)) {
                    $this->setData($result);
                }
            }
        }

        return $this;
    }
}
