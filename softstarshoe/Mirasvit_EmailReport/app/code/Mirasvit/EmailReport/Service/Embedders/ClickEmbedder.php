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


use Mirasvit\Email\Api\Data\QueueInterface;

class ClickEmbedder extends AbstractEmbedder
{
    /**
     * {@inheritDoc}
     */
    public function embed(QueueInterface $queue, $content)
    {
        $params = $this->getParams($queue);
        if ($params) {
            $matches = array();
            if(preg_match_all('/<a\s[^>]*href=([\"\']??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU', $content, $matches)) {
                foreach ($matches[2] as $key => $link) {
                    $newLink = $this->linkEmbedder->embed($link, $params);
                    if ($newLink) {
                        $from = $matches[0][$key];
                        $to   = str_replace('href="'.$link.'"', 'href="'.$newLink.'"', $from);

                        $content = str_replace($from, $to, $content);
                    }
                }
            }
        }

        return $content;
    }
}
