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

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Mirasvit\Seo\Observer\Canonical as CanonicalObserver;
use Mirasvit\Seo\Model\Config;

class Analyzer extends AbstractHelper
{
    const STATUS_SUCCESS = 'success';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR = 'error';

    /**
     * @var CanonicalObserver
     */
    protected $canonicalObserver;

    /**
     * @param CanonicalObserver $canonicalObserver
     * @param Context           $context
     */
    public function __construct(
        CanonicalObserver $canonicalObserver,
        Context $context
    ) {
        $this->canonicalObserver = $canonicalObserver;

        parent::__construct($context);
    }

    /**
     * @param string $body
     * @return array
     */
    public function getCanonicalStatus($body)
    {
        $url = '';
        $count = 0;
        $originalUrl = $this->canonicalObserver->getCanonicalUrl();

        preg_match_all('/<link\s*rel="canonical"\s*href="(.*?)"\s*\/>/', $body, $canonicalArray);

        if (isset($canonicalArray[1][0])) {
            $count = count($canonicalArray[1]);
            $url = $canonicalArray[1][0];
        }

        if ($count == 0) {
            return [
                'status'  => self::STATUS_ERROR,
                'value'   => $url,
                'message' => __('Missing canonical URL.'),
            ];
        } elseif ($count > 1) {
            return [
                'status'  => self::STATUS_ERROR,
                'value'   => $url,
                'message' => __('%1 canonical on the page.', $count),
            ];
        } elseif ($url && $originalUrl && $originalUrl != $url) {
            return [
                'status'  => self::STATUS_WARNING,
                'value'   => $url,
                'message' => __('Canonical created not using Mirasvit SEO extension.'),
            ];
        }

        return [
            'status' => self::STATUS_SUCCESS,
            'value'  => $url,
        ];
    }

    /**
     * @param string $body
     * @return array
     */
    public function getHeaderStatus($body)
    {
        $count = 0;
        $tags = [];
        $headers = [];
        $pattern = '/<h1(.*?)>(.*?)<\/h1>/ims';

        preg_match_all($pattern, $body, $tags[]);

        foreach ($tags as $title) {
            if (isset($title[0][0])) {
                $headers[] = strip_tags($title[0][0]);
            }
        }

        if ($headers) {
            $count = count($headers);
        }

        if ($count == 0) {
            return [
                'status'  => self::STATUS_ERROR,
                'message' => __('There is no H1 tag on the page.'),
                'value'   => '',
            ];
        } elseif ($count == 1) {
            return [
                'status'  => self::STATUS_SUCCESS,
                'message' => __('One H1 tag'),
                'value'   => implode(', ', $headers),
            ];
        } else {
            return [
                'status'  => self::STATUS_WARNING,
                'message' => __('%1 H1 tags.', $count),
                'value'   => implode(', ', $headers),
            ];
        }
    }

    /**
     * @param string $body
     * @return array
     */
    public function getMetaTitleStatus($body)
    {
        $meta = $this->getMetaTags($body);
        if (isset($meta['title'])) {
            $length = strlen($meta['title']);
            if ($length > Config::META_TITLE_MAX_LENGTH) {
                return [
                    'status'  => self::STATUS_WARNING,
                    'value'   => $meta['title'],
                    'message' => __(
                        'Length = %1. Recommended length up to %2 characters.',
                        $length,
                        Config::META_TITLE_MAX_LENGTH
                    ),
                ];
            } else {
                return [
                    'status' => self::STATUS_SUCCESS,
                    'value'  => $meta['title']
                ];
            }
        } else {
            return [
                'status'  => self::STATUS_ERROR,
                'message' => __('Meta title not defined.'),
                'value'   => '',
            ];
        }
    }

    /**
     * @param string $body
     * @return array
     */
    public function getMetaDescriptionStatus($body)
    {
        $meta = $this->getMetaTags($body);
        if (isset($meta['description'])) {
            $length = strlen($meta['description']);
            if ($length > Config::META_DESCRIPTION_MAX_LENGTH) {
                return [
                    'status'  => self::STATUS_WARNING,
                    'value'   => $meta['description'],
                    'message' => __(
                        'Length = %1. Recommended length up to %2 characters.',
                        $length,
                        Config::META_DESCRIPTION_MAX_LENGTH
                    ),
                ];
            } else {
                return [
                    'status' => self::STATUS_SUCCESS,
                    'value'  => $meta['description']
                ];
            }
        } else {
            return [
                'status'  => self::STATUS_ERROR,
                'message' => __('Meta description not defined.'),
                'value'   => '',
            ];
        }
    }

    /**
     * @param string $body
     * @return array
     */
    public function getMetaKeywordsStatus($body)
    {
        $meta = $this->getMetaTags($body);
        if (isset($meta['keywords'])) {
            $data = $this->analyzeKeywords($meta['keywords'], $body);
            return [
                'status'   => self::STATUS_SUCCESS,
                'value'    => $meta['keywords'],
                'keywords' => $data,
            ];
        } else {
            return [
                'status'  => self::STATUS_ERROR,
                'message' => __('Meta keywords not defined.'),
                'value'   => '',
            ];
        }
    }

    /**
     * @param string $body
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getImagesStatus($body)
    {
        $withEmptyAlt = [];
        $withoutAlt = [];
        $imgPrepared = [];

        preg_match_all('/<img[^>]+>/i', $body, $imagesData);
        if (isset($imagesData[0]) && $imagesData[0]) {
            $img = [];


            foreach ($imagesData[0] as $imgTag) {
                preg_match_all('/(alt|src)=("[^"]*")/i', $imgTag, $img[$imgTag]);
            }

            if ($img) {
                foreach ($img as $imgKey => $imgArray) {
                    if (isset($imgArray[0]) && isset($imgArray[1]) && isset($imgArray[2])) {
                        foreach ($imgArray[1] as $tagKey => $tag) {
                            $tagValue = trim($imgArray[2][$tagKey], '"..\'');
                            $imgPrepared[$imgKey][$tag] = $tagValue;
                        }
                    }
                }
            }
            if ($imgPrepared) {
                foreach ($imgPrepared as $imgKey => $imgTagsArray) {
                    if (!isset($imgTagsArray['src'])) {
                        continue;
                    }
                    if (array_key_exists('alt', $imgTagsArray) && $imgTagsArray['alt'] == '') {
                        $withEmptyAlt[] = $imgTagsArray['src'];
                    } elseif (!array_key_exists('alt', $imgTagsArray)) {
                        $withoutAlt[] = $imgTagsArray['src'];
                    }
                }
            }
        }

        if (count($withEmptyAlt) + count($withoutAlt) > 0) {
            return [
                'status'  => self::STATUS_WARNING,
                'value'   => '',
                'message' => __('Some alt tags are empty or missing.'),
                'images'  => [
                    'stat'         => [
                        __('Total number     of images')->__toString()      => count($imgPrepared),
                        __('Images without alt attribute')->__toString()    => count($withoutAlt),
                        __('Images with empty alt attribute')->__toString() => count($withEmptyAlt),
                    ],
                    'withoutAlt'   => $withoutAlt,
                    'withEmptyAlt' => $withEmptyAlt,
                ],
            ];
        } else {
            return [
                'status'  => self::STATUS_SUCCESS,
                'value'   => '',
                'message' => __('%1 images with correct alt attribute.', count($imgPrepared)),
            ];
        }
    }

    /**
     * @param string $body
     * @return array
     */
    protected function getMetaTags($body)
    {
        $meta = [];

        # <meta
        $pattern = '
              ~<\s*meta\s
                (?=[^>]*?
                \b(?:name|property|http-equiv)\s*=\s*
                (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
                ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
              )
              [^>]*?\bcontent\s*=\s*
                (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
                ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
              [^>]*>
              ~ix
        ';

        if (preg_match_all($pattern, $body, $out)) {
            $meta = array_combine($out[1], $out[2]);
        }

        if (preg_match('/<title>(.*?)<\/title>/ims', $body, $out)) {
            $meta['title'] = $out[1];
        }

        return $meta;
    }

    /**
     * @param string $keywords
     * @param string $body
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function analyzeKeywords($keywords, $body)
    {
        $occurrences = [];
        if ($keywords) {
            $body = strtolower(preg_replace('/<head>(.*?)<\/head>/ims', '', $body, 1));

            $keywords = explode(',', strtolower($keywords));
            $keywords = array_map('trim', $keywords);
            $keywords = array_filter($keywords);

            $nextSymbol = ['', ' ', ',', '.', '!', '?', "\n", "\r", "\r\n", '<'];    // symbols after the word
            $prevSymbol = [',', ' ', "\n", "\r", "\r\n", '>']; // symbols before the word
            foreach ($keywords as $keyword) {
                $size = 0;
                $keywordCount = 0;
                $explodeSource = explode($keyword, $body);
                $sizeExplodeSource = count($explodeSource);

                foreach (array_keys($explodeSource) as $keySource) {
                    ++$size;
                    if (($size < $sizeExplodeSource)
                        && (((!empty($explodeSource[$keySource + 1][0]))
                                && (in_array($explodeSource[$keySource + 1][0], $nextSymbol)))
                            || (empty($explodeSource[$keySource + 1][0])))
                        && ((empty($explodeSource[$keySource][strlen($explodeSource[$keySource]) - 1]))
                            || ((!empty($explodeSource[$keySource][strlen($explodeSource[$keySource]) - 1]))
                                && (in_array(
                                    $explodeSource[$keySource][strlen($explodeSource[$keySource]) - 1],
                                    $prevSymbol
                                ))))
                    ) {
                        ++$keywordCount;
                    }
                }
                $occurrences[$keyword] = $keywordCount;
            }
        }

        $sum = array_sum($occurrences);

        foreach ($occurrences as $keyword => $number) {
            if ($sum > 0) {
                $occurrences[$keyword] = [
                    'count'   => $number,
                    'percent' => ($number / $sum) * 100,
                ];
            } else {
                $occurrences[$keyword] = [
                    'count'   => 0,
                    'percent' => 0,
                ];
            }
        }

        return $occurrences;
    }
}