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
 * @package   mirasvit/module-email-report
 * @version   1.0.5
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Service\Embedders;


use Mirasvit\EmailReport\Api\Service\LinkEmbedderInterface;

class LinkEmbedder implements LinkEmbedderInterface
{
    /**
     * {@inheritDoc}
     */
    public function embed($link, $params)
    {
        $newLink = false;
        $components = parse_url($link);

        if (is_array($params)) {
            $params = implode('&', $params);
        }

        if (isset($components['path']) && isset($components['host'])) {
            if (isset($components['query'])) {
                $newLink = $link.'&'.$params;
            } else {
                $newLink = $link.'?'.$params;
            }

            if (isset($components['fragment'])) {
                $newLink = str_replace('#'.$components['fragment'], '', $newLink).'#'.$components['fragment'];
            }
        }

        return $newLink;
    }
}
