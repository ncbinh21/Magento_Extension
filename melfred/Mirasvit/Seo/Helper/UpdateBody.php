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



namespace Mirasvit\Seo\Helper;

class UpdateBody extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param string $body
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function hasDoctype(&$body)
    {
        $doctypeCode = ['<!doctype html', '<html', '<?xml'];
        foreach ($doctypeCode as $doctype) {
            if (stripos(trim($body), $doctype) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $body
     * @param string $seoMetaDescription
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function replaceMetaDescription(&$body, $seoMetaDescription)
    {
        $seoMetaDescription = $this->prepareMeta($seoMetaDescription);
        $pattern = '/<meta name="description" content="(.*?)"\\/>/ims';
        $replacement = '<meta name="description" content="' . $seoMetaDescription . '"/>';
        $body = preg_replace($pattern, $replacement, $body, 1);
    }

    /**
     * @param string $body
     * @param string $seoMetaKeywords
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function replaceMetaKeywords(&$body, $seoMetaKeywords)
    {
        $seoMetaKeywords = $this->prepareMeta($seoMetaKeywords);
        $pattern = '/<meta name="keywords" content="(.*?)"\\/>/ims';
        $replacement = '<meta name="keywords" content="' . $seoMetaKeywords . '"/>';
        $body = preg_replace($pattern, $replacement, $body, 1);
    }

    /**
     * @param string $body
     * @param string $seoMetaTitle
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function replaceMetaTitle(&$body, $seoMetaTitle)
    {
        $pattern = '/<title>(.*?)<\\/title>/ims';
        $replacement = '<title>' . $seoMetaTitle . '</title>';
        $body = preg_replace($pattern, $replacement, $body, 1);
    }


    /**
     * @param string $body
     * @param string $seoTitle
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function replaceFirstLevelTitle(&$body, $seoTitle)
    {
        $firstLevelTitle = [];
        $firstLevelTitlePrepared = [];
        $patterns = ['/<h1(.*?)>(.*?)<\/h1>/ims', '/<h1>(.*?)<\/h1>/ims'];

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $body, $firstLevelTitle[]);
        }

        foreach ($firstLevelTitle as $title) {
            if (isset($title[0][0]) && strpos($title[0][0], '</h1>') !== false) {
                $firstLevelTitlePrepared[] = $title[0][0];
            }
        }

        if (isset($firstLevelTitlePrepared[0])
            && $firstLevelTitlePrepared[0]
            && count($firstLevelTitlePrepared) == 1
            && ($titleText = trim(strip_tags($firstLevelTitlePrepared[0])))
            && $titleText != $seoTitle
            ) {
            $title = str_replace($titleText, $seoTitle, $firstLevelTitlePrepared[0]);
            $body = str_replace($firstLevelTitlePrepared[0], $title, $body);
        }
    }

    /**
     * @param string $body
     * @param string $robots
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function replaceRobots(&$body, $robots)
    {
        $pattern = '/<meta name="robots" content="(.*?)"\\/>/ims';
        $replacement = '<meta name="robots" content="' . $robots . '"/>';
        $body = preg_replace($pattern, $replacement, $body, 1);
    }

    /**
     * @param string $body
     * @param string $canonicalTag
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addCanonicalTag(&$body, $canonicalTag)
    {
        $pattern = '/<link  rel="stylesheet" type="text\\/css"/ims';
        $replacement = $canonicalTag . ' <link  rel="stylesheet" type="text/css"';
        $body = preg_replace($pattern, $replacement, $body, 1);
    }

    /**
     * @param string $body
     * @param string $canonicalTag
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addPrevNextLink(&$body, $prevNextLink)
    {
        $pattern = '/<link  rel="stylesheet" type="text\\/css"/ims';
        $replacement = $prevNextLink . ' <link  rel="stylesheet" type="text/css"';
        $body = preg_replace($pattern, $replacement, $body, 1);
    }

    /**
     * @param string $meta
     * @return string
     */
    protected function prepareMeta($meta)
    { 
        return str_replace('"', '\'', $meta);
    }
}
