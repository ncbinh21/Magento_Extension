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



namespace Mirasvit\Seo\Plugin\Event;

use Magento\Framework\App\ResponseInterface;

/**
 * Builtin cache processor
 */
class ApplyResponsePlugin
{
    /**
     * @var \Mirasvit\Seo\Observer\Snippet
     */
    private $seoSnippet;

    /**
     * @var \Mirasvit\Seo\Observer\Opengraph
     */
    private $opengraph;

    /**
     * @var \Mirasvit\Seo\Observer\UpdateMeta
     */
    private $updateMeta;

    /**
     * ApplyResponsePlugin constructor.
     * @param \Mirasvit\Seo\Observer\Snippet $seoSnippet
     * @param \Mirasvit\Seo\Observer\Opengraph $opengraph
     * @param \Mirasvit\Seo\Observer\UpdateMeta $updateMeta
     * @param \Mirasvit\Seo\Api\Service\Twitter\TwitterCardInterface $twitterCard
     */
    public function __construct(
        \Mirasvit\Seo\Observer\Snippet $seoSnippet,
        \Mirasvit\Seo\Observer\Opengraph $opengraph,
        \Mirasvit\Seo\Observer\UpdateMeta $updateMeta,
        \Mirasvit\Seo\Api\Service\Twitter\TwitterCardInterface $twitterCard
    ) {
        $this->seoSnippet = $seoSnippet;
        $this->opengraph = $opengraph;
        $this->updateMeta = $updateMeta;
        $this->twitterCard = $twitterCard;
    }


    /**
     * Modify and cache application response
     *
     * @param Magento\Framework\App\PageCache\Kernel $subject
     * @param ResponseInterface $response
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
     public function beforeProcess($subject, ResponseInterface $response)
     {
         $response = $this->seoSnippet->addProductSnippets(false, $response);
         $response = $this->opengraph->modifyHtmlResponse(false, $response);
         $response = $this->updateMeta->modifyHtmlResponseMeta(false, $response);
         $response = $this->twitterCard->addTwitterCard(false, false, $response);
     }

}
