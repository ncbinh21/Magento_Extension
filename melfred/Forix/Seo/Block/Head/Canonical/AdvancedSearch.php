<?php

namespace Forix\Seo\Block\Head\Canonical;

use Forix\Seo\Block\Head\Canonical;

class AdvancedSearch extends Canonical
{
    protected function getCanonicalHtml()
    {
        $url = $this->getUrl('catalogsearch/advanced/result/');
        $html = sprintf('<link rel="canonical" href="%s" />' . PHP_EOL, $url);
        return $html;
    }
}
