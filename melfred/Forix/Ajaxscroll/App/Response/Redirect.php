<?php
/**
 * Response redirector
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Ajaxscroll\App\Response;

use Magento\Store\Api\StoreResolverInterface;

class Redirect extends \Magento\Store\App\Response\Redirect
{

    /**
     * Normalize path to avoid wrong store change
     *
     * @param string $refererUrl
     * @return string
     */
    protected function normalizeRefererUrl($refererUrl)
    {
        if (!$refererUrl || !filter_var($refererUrl, FILTER_VALIDATE_URL)) {
            return $refererUrl;
        }

        $redirectParsedUrl = parse_url($refererUrl);
        $refererQuery = [];

        if (!isset($redirectParsedUrl['query'])) {
            return $refererUrl;
        }

        parse_str($redirectParsedUrl['query'], $refererQuery);
        if (isset($refererQuery['isAjax'])) {
            unset($refererQuery['isAjax']);
        }
        $refererQuery = $this->normalizeRefererQueryParts($refererQuery);
        $normalizedUrl = $redirectParsedUrl['scheme']
            . '://'
            . $redirectParsedUrl['host']
            . (isset($redirectParsedUrl['port']) ? ':' . $redirectParsedUrl['port'] : '')
            . $redirectParsedUrl['path']
            . ($refererQuery ? '?' . http_build_query($refererQuery) : '');

        return $normalizedUrl;
    }
}
