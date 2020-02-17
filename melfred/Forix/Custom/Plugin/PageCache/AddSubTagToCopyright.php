<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 04/09/2018
 * Time: 10:40
 */

namespace Forix\Custom\Plugin\PageCache;

use Magento\Framework\App\ResponseInterface;

class AddSubTagToCopyright
{
    protected $htmlSubTagHelper;

    public function __construct(
        \Forix\Custom\Helper\HtmlSubTagHelper $htmlSubTagHelper)
    {
        $this->htmlSubTagHelper = $htmlSubTagHelper;
    }

    public function beforeProcess($subject, ResponseInterface $response)
    {
        $body = $response->getContent();
        $body = $this->htmlSubTagHelper->addSubTag($body);
        $response->setContent($body);
        return [$response];
    }

}