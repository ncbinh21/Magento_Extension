<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoSitemap\Block\Map;

class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * @param \Magento\Framework\Data\Collection $collection
     * @return void
     */
    public function setCollection($collection)
    {
        if ((int) $this->getLimit()) {
            $collection->setPageSize($this->getLimit());
        }
        parent::setCollection($collection);
    }

    /**
     * @return string
     */
    public function getPreviousPageUrl()
    {
        return $this->getPageUrl($this->getCollection()->getCurPage() - 1);
    }

    /**
     * @return string
     */
    public function getNextPageUrl()
    {
        return $this->getPageUrl($this->getCollection()->getCurPage() + 1);
    }

    /**
     * @param array $params
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (count($params) > 0 && $params['p'] != 1) {
            $query = http_build_query($params);
            $url .= '?'.$query;
        }

        return $url;
    }
}
