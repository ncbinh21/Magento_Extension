<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\Bannerslider\Model\ResourceModel\Report;

/**
 * Report Collection
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    const REPORT_TYPE_ALL_SLIDER = '1';
    const REPORT_TYPE_PER_SLIDER = '2';

    protected function _construct()
    {
        $this->_init('Forix\Bannerslider\Model\Report', 'Forix\Bannerslider\Model\ResourceModel\Report');
    }

    /**
     * load slider and banner information to report
     */
    public function reportClickAndImpress($reportType)
    {
        $this->getSelect()->joinLeft(
            ['table_banner' => $this->getTable('forix_bannerslider_banner')],
            'main_table.banner_id = table_banner.banner_id',
            ['banner_name' => 'table_banner.name', 'banner_url' => 'table_banner.click_url']
        )->joinLeft(
            ['table_slider' => $this->getTable('forix_bannerslider_slider')],
            'main_table.slider_id = table_slider.slider_id',
            ['slider_title' => 'table_slider.title']
        )->columns('SUM(main_table.clicks) AS banner_click')
            ->columns('SUM(main_table.impmode) AS banner_impress');

        if ($reportType == self::REPORT_TYPE_ALL_SLIDER) {
            $this->getSelect()->group('main_table.banner_id');
        } else if ($reportType == self::REPORT_TYPE_PER_SLIDER) {
            $this->getSelect()->group('main_table.slider_id')
                ->group('main_table.banner_id');
        }

        return $this;
    }
}