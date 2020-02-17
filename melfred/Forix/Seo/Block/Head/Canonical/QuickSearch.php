<?php

namespace Forix\Seo\Block\Head\Canonical;

use Forix\Seo\Block\Head\Canonical;

class QuickSearch extends Canonical
{
    protected function getCanonicalHtml()
    {
        $url = $this->getUrl('catalogsearch/result/');
        $html = sprintf('<link rel="canonical" href="%s" />' . PHP_EOL, $url);
        return $html;
    }
}
