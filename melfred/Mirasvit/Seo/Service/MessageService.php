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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Service;

use Mirasvit\Seo\Api\Service\MessageInterface as SeoMessageInterface;
use Magento\Framework\Message\ManagerInterface;

class MessageService implements SeoMessageInterface
{
    /**
     * @param Version $version
     */
    public function __construct(
        ManagerInterface $messageManager
    ) {
        $this->messageManager = $messageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function addNoticeWithUrl($notice, $url)
    {
        $this->messageManager->addComplexNoticeMessage(SeoMessageInterface::NOTICE_URL_IDENTIFIER,
            [
                'seo_notice' => $notice,
                'seo_url' => $url
            ]
        );
    }

    //Template Section

    /**
     * {@inheritdoc}
     */
    public function addProductTemplateNotice()
    {
        $notice = 'If option "Use meta tags from products if they are not empty" set to "Yes"
                        it have influence on this template. More about that option you can read ';
        $url = 'https://docs.mirasvit.com/module-seo/current/seo/settings';
        $this->addNoticeWithUrl($notice, $url);
    }

    /**
     * {@inheritdoc}
     */
    public function addCategoryTemplateNotice()
    {
        $notice = 'If option "Use meta tags from categories if they are not empty" set to "Yes"
                        it have influence on this template. More about that option you can read ';
        $url = 'https://docs.mirasvit.com/module-seo/current/seo/settings';
        $this->addNoticeWithUrl($notice, $url);
    }
}

