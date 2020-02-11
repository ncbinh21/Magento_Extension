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



namespace Mirasvit\Seo\Plugin\ChangeResult;

use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\Controller\ResultInterface;

class ChangeContent
{
    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Mirasvit\Seo\Helper\UpdateBody
     */
    protected $updateBody;

    /**
     * @var ResponseHttp
     */
    protected $response;

    /**
     * @var \Mirasvit\Seo\Observer\Canonical
     */
    protected $canonical;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param ResponseHttp $response
     * @param \Mirasvit\Seo\Helper\Data $seoData
     * @param \Mirasvit\Seo\Helper\UpdateBody $updateBody
     * @param \Magento\Framework\Registry $registry
     * @param \Mirasvit\Seo\Observer\Canonical $canonical
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        ResponseHttp $response,
        \Mirasvit\Seo\Helper\Data $seoData,
        \Mirasvit\Seo\Helper\UpdateBody $updateBody,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Seo\Observer\Canonical $canonical,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->response = $response;
        $this->seoData = $seoData;
        $this->updateBody = $updateBody;
        $this->registry = $registry;
        $this->canonical = $canonical;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param ResultInterface $subject
     * @param ResultInterface $result
     * @return ResultInterface
     */
    public function afterRenderResult(
        ResultInterface $subject,
        ResultInterface $result
    ) {
        if (!($subject instanceof Json)) {
            $this->addCanonical();
            if (!$this->registry->registry('current_product')
                || $this->seoData->isIgnoredActions()) {
                    return $result;
            }

            $seo = $this->seoData->getCurrentSeo();

            if (!$seo || !trim($seo->getTitle()) || !is_object($this->response)) {
                return $result;
            }

            $body = $this->response->getBody();

            if (!$this->updateBody->hasDoctype($body)) {
                return $result;
            }

            $this->updateBody->replaceFirstLevelTitle($body, $seo->getTitle());

            $this->response->setBody($body);
        }

            return $result;
    }


    /**
     * @return bool
     */
    public function addCanonical() 
    {
        $blogPages = ['blog_index_index', 'blog_post_view', 'blog_category_view'];
        $fullActionCode = $this->seoData->getFullActionCode();

        if (in_array($fullActionCode, $blogPages)
            && $this->moduleManager->isEnabled('Magefan_Blog')
            && ($canonical = $this->canonical->getCanonicalUrl())
            && ($canonicalTag = '<link  rel="canonical" href="' . $canonical . '" />')
            && ($body = $this->getBody())) {
                $this->updateBody->addCanonicalTag($body, $canonicalTag);
                $this->response->setBody($body);
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function getBody() 
    {
        $body = $this->response->getBody();

        if (!$this->updateBody->hasDoctype($body)) {
            $body = false;
        }

        return $body;
    }

}
