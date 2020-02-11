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



namespace Mirasvit\SeoAutolink\Helper;
use Mirasvit\SeoAutolink\Model\Link;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Replace extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\SeoAutolink\Model\LinkFactory
     */
    protected $linkFactory;

    /**
     * @var \Mirasvit\SeoAutolink\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Core\Helper\String
     */
    protected $coreString;

    /**
     * @var \Mirasvit\SeoAutolink\Helper\Pattern
     */
    protected $seoAutolinkPattern;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Mirasvit\SeoAutolink\Model\LinkFactory    $linkFactory
     * @param \Mirasvit\SeoAutolink\Model\Config         $config
     * @param \Mirasvit\Core\Api\TextHelperInterface     $coreString
     * @param \Mirasvit\SeoAutolink\Helper\Pattern       $seoAutolinkPattern
     * @param \Magento\Framework\App\Helper\Context      $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry                $registry
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     */
    public function __construct(
        \Mirasvit\SeoAutolink\Model\LinkFactory $linkFactory,
        \Mirasvit\SeoAutolink\Model\Config $config,
        \Mirasvit\Core\Api\TextHelperInterface $coreString,
        \Mirasvit\SeoAutolink\Helper\Pattern $seoAutolinkPattern,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Mirasvit\SeoAutolink\Helper\Debug $debugHelper
    ) {
        $this->linkFactory = $linkFactory;
        $this->config = $config;
        $this->coreString = $coreString;
        $this->seoAutolinkPattern = $seoAutolinkPattern;
        $this->context = $context;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->debugHelper = $debugHelper;
        parent::__construct($context);
    }

    const MAX_NUMBER = 999999;

    /**
     * @var bool
     */
    protected $_isSkipLinks;

    /**
     * @var int
     */
    protected $_sizeExplode             = 0;

    /**
     * @var bool
     */
    protected $_isExcludedTags          = true;

    /**
     * @var array
     */
    protected $_replacementsCountGlobal = [];

    /**
     * @var array
     */
    protected $debug = [];

    /**
     * @var int
     */
    protected $currentNumberOfLinks = 0;


    /**
     * @return \Mirasvit\SeoAutolink\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        return (!$this->storeManager->getStore()) ? 1 : $this->storeManager->getStore()->getId();
    }

    /**
     * Returns value of setting "Links limit per page"
     *
     * @return int
     */
    public function getMaxLinkPerPage()
    {
        if ($max = $this->getConfig()->getLinksLimitPerPage($this->getStoreId())) {
            return $max;
        }
        return self::MAX_NUMBER;
    }


    /**
     * Returns collection of links with keywords which present in our text.
     * Not ALL possible links.
     * try get links with newer query, if returns SQLERROR
     * (for older Magento like 1.4 and specific MySQL configurations) -
     * get links with older query for backward compatibility
     *
     * @param string $text
     * @return Link[]
     */
    public function getLinks($text)
    {
        \Magento\Framework\Profiler::start('seoautolink_getLinks');

        $textArrayWithMaxSymbols = $this->splitText($text); //return array
        $where = [];
        foreach ($textArrayWithMaxSymbols as $splitTextVal) {
            $where[] = "lower('".addslashes($splitTextVal)."') LIKE CONCAT("."'%'".', lower(keyword), '."'%'".')';
        }

        $links = $this->getLinksCollection();
        $links->getSelect()->where(implode(' OR ', $where))->order('sort_order ASC');

        try {
            count($links); //need to load collection to catch SQLERROR if occured
        } catch (\Exception $e) {
            $links = $this->getLinksCollection();
            $links->getSelect()->where("lower(?) LIKE CONCAT('%', lower(keyword), '%')", $text)
                ->order( ['LENGTH(main_table.keyword) desc'] ); //we need to replace long keywords firstly
        }
        \Magento\Framework\Profiler::stop('seoautolink_getLinks');

        return $links;
    }

    /**
     * Prepare collection acceptable for both variants of SQL queries.
     *
     * @return \Mirasvit\SeoAutolink\Model\Link[]
     */
    private function getLinksCollection()
    {
        $links = $this->linkFactory->create()
            ->getCollection()
            ->addActiveFilter()
            ->addStoreFilter($this->storeManager->getStore());

        return $links;
    }

    /**
     * Replace special chars in text to their altenatives
     *
     * @param string $source
     * @return string
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function replaceSpecialCharacters($source)
    {
        // substitute some special html characters with their 'real' value
        $searchTamp = [
            '&amp;Eacute;',
            '&amp;Euml;',
            '&amp;Oacute;',
            '&amp;eacute;',
            '&amp;euml;',
            '&amp;oacute;',
            '&amp;Agrave;',
            '&amp;Egrave;',
            '&amp;Igrave;',
            '&amp;Iacute;',
            '&amp;Iuml;',
            '&amp;Ograve;',
            '&amp;Ugrave;',
            '&amp;agrave;',
            '&amp;egrave;',
            '&amp;igrave;',
            '&amp;iacute;',
            '&amp;iuml;',
            '&amp;ograve;',
            '&amp;ugrave;',
            '&amp;Ccedil;',
            '&amp;ccedil;',
            '&amp;ecirc;',
        ];
        $replaceTamp = ['É',
            'Ë',
            'Ó',
            'é',
            'ë',
            'ó',
            'À',
            'È',
            'Ì',
            'Í',
            'Ï',
            'Ò',
            'Ù',
            'à',
            'è',
            'ì',
            'í',
            'ï',
            'ò',
            'ù',
            'Ç',
            'ç',
            'ê',
        ];
        $searchT = ['&Eacute;',
            '&Euml;',
            '&Oacute;',
            '&eacute;',
            '&euml;',
            '&oacute;',
            '&Agrave;',
            '&Egrave;',
            '&Igrave;',
            '&Iacute;',
            '&Iuml;',
            '&Ograve;',
            '&Ugrave;',
            '&agrave;',
            '&egrave;',
            '&igrave;',
            '&iacute;',
            '&iuml;',
            '&ograve;',
            '&ugrave;',
            '&Ccedil;',
            '&ccedil;',
        ];
        $replaceT = ['É',
            'Ë',
            'Ó',
            'é',
            'ë',
            'ó',
            'À',
            'È',
            'Ì',
            'Í',
            'Ï',
            'Ò',
            'Ù',
            'à',
            'è',
            'ì',
            'í',
            'ï',
            'ò',
            'ù',
            'Ç',
            'ç',
        ];

        $source = str_replace($searchTamp, $replaceTamp, $source);
        $source = str_replace($searchT, $replaceT, $source);

        return $source;
    }

    /**
     * Main entry point. Inserts links into text.
     *
     * @param string $text
     *
     * @return string
     */
    public function addLinks($text)
    {
        if (strpos($this->context->getUrlBuilder()->getCurrentUrl(), '/checkout/onepage/')
            || strpos($this->context->getUrlBuilder()->getCurrentUrl(), 'onestepcheckout')) {
            return $text;
        }

        if ($this->checkSkipLinks() === true) {
            return $text;
        }
        $this->debug['links'] = [];

        $text = $this->replaceSpecialCharacters($text);
        $links = $this->getLinks($text);
        $text = $this->_addLinks($text, $links);
        $text = $this->getDebugMessage().$text;
        return $text;
    }

    /**
     * Inserts links into text
     *
     * @param string $text
     * @param array $links
     * @param bool|int $replacementCountForTests - max number of replaced words. used only for tests.
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function _addLinks($text, $links, $replacementCountForTests = false)
    {
        if (!$links || count($links) == 0) {
            return $text;
        }

        \Magento\Framework\Profiler::start('seoautolink_addLinks');


        foreach ($links  as $link) {
            if (strlen($link->getKeyword()) <= 1) { //one letter can't be in autolinks
               continue;
            }
            /** @var Link $link */
            $replaceKeyword = preg_quote($link->getKeyword()); // Escaping special characters in a keyword
            $this->debug['current_link'] = $link;
            $urltitle = $link->getUrlTitle() ? "title='{$link->getUrlTitle()}' " : '';
            $nofollow = $link->getIsNofollow() ? 'rel=\'nofollow\' ' : '';
            $target = $link->getUrlTarget() ? "target='{$link->getUrlTarget()}' " : '';
            $html = "<a href='{$this->_prepareLinkUrl($link->getUrl())}'"
                        . " {$urltitle}{$target}{$nofollow}class='autolink' >"
                        . $link->getKeyword()."</a>";

            $maxReplacements = self::MAX_NUMBER;
            if ($link->getMaxReplacements() > 0) {
                $maxReplacements = $link->getMaxReplacements();
            }
            if ($replacementCountForTests) { //for tests
                $maxReplacements = $replacementCountForTests;
            }

            $direction = 0;
            switch ($link->getOccurence()) {
                case \Mirasvit\SeoAutolink\Model\Config\Source\Occurence::FIRST:
                    $direction = 0;
                    break;
                case \Mirasvit\SeoAutolink\Model\Config\Source\Occurence::LAST:
                    $direction = 1;
                    break;
                case \Mirasvit\SeoAutolink\Model\Config\Source\Occurence::RANDOM:
                    $direction = rand(0, 1);
                    break;
            }

            $pregPatterns = $this->getPatterns();
            $pl = new Replace\TextPlaceholder($text, $pregPatterns);
            $text = $pl->getTokenizedText();

//            $this->debugHelper->startTimer('replace');
            $text = $this->replace($html, $text, $maxReplacements, $replaceKeyword, $direction);
//            $this->debugHelper->stopTimer('replace');
//            $this->debugHelper->logTimer('replace');

            $translationTable = $pl->getTranslationTableArray();
            $text = $this->_restoreSurceByTranslationTable($translationTable, $text);

            $this->debug['links'][] = $this->debug['current_link'];
        }
        \Magento\Framework\Profiler::stop('seoautolink_addLinks');

        return $text;
    }

    /**
     * Returns link url with base url (need to get correct store code in url)
     *
     * @param string $url
     * @return string
     */
    protected function _prepareLinkUrl($url)
    {
        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            if (substr($url, 0, 1) == '/') {
                $url = substr($url, 1);
            }
            $url = $baseUrl . $url;
        }

        return $url;
    }

    /**
     * Returns array of patterns, which will be used to find and replace keywords
     *
     * @return array
     */
    protected function getPatterns()
    {
        $patternsForExclude = $this->getExcludedAutoTags();

        // matches for these expressions will be replaced with a unique placeholder
        $pregPatterns = [
            '#<!--.*?-->#s'       // html comments
            , '#<a[^>]*>.*?</a>#si' // html links
            , '#<[^>]+>#',           // generic html tag
        ];

        if ($patternsForExclude) {
            $pregPatterns = array_merge($patternsForExclude, $pregPatterns);
        }

        return $pregPatterns;
    }


    /**
     * Reconstruct the original text
     *
     * @param array $translationTable
     * @param string $source
     * @return string
     */
    protected function _restoreSurceByTranslationTable($translationTable, $source)
    {
        foreach ($translationTable as $key => $value) {
            $source = str_replace($key, $value, $source);
        }

        return $source;
    }

    /**
     * Replace words and left the same cases
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @param string $replace - html which will replace the keyword
     * @param string $source - initial text
     * @param int $maxReplacements - max number of replacements in this text.
     * @param bool $replaceKeyword - keyword which will be replaced
     * @param bool $direct - replace direction (from begin or from end of the text)
     * @return string
     */
    protected function replace($replace, $source, $maxReplacements, $replaceKeyword = false, $direct = false)
    {
        if ($this->currentNumberOfLinks >= $this->getMaxLinkPerPage()) { //Links limit per page
            return $source;
        }

        if ($maxReplacements > 0 && $this->getRelpacementCount($replaceKeyword) > $maxReplacements) {
            return $source;
        }

        $maxReplacements -= $this->getRelpacementCount($replaceKeyword);

        preg_match_all('/'.preg_quote($replaceKeyword, '/').'/i',
            $source,
            $replaceKeywordVariations,
            PREG_OFFSET_CAPTURE);

        if (isset($replaceKeywordVariations[0])) {
            $keywordVariations = $replaceKeywordVariations[0];
            if (!empty($keywordVariations)) {
                if ($direct == 1) {
                    $keywordVariations = array_slice($keywordVariations, -$maxReplacements);
                } else {
                    $keywordVariations = array_slice($keywordVariations, 0, $maxReplacements);
                }
                foreach ($keywordVariations as $keywordValue) {
                    if ($this->currentNumberOfLinks >= $this->getMaxLinkPerPage()) { //Links limit per page
                        break;
                    }

                    $replaceForVariation = preg_replace(
                        '/(\\<a.*?\\>)(.*?)(\\<\\/a\\>)/', $this->prepareReplacement($keywordValue[0]), $replace
                    );
                    $source = $this->addLinksToSource(
                        $maxReplacements, $direct, $source, $keywordValue[0], $replaceForVariation
                    );
                }
                $this->_sizeExplode = 0;
            }
        }

        return $source;
    }

    /**
     * @param string $keyword
     * @return string
     */
    protected function _mbSubstr($keyword)
    {
        if (function_exists('mb_substr')) {
            return mb_substr($keyword, 0, 1);
        }

        return substr($keyword, 0, 1);
    }

    /**
     * @param string $keyword
     * @return string
     */
    public function prepareReplacement($keyword)
    {
        if (is_numeric($this->_mbSubstr($keyword))) {
            $replacement = "$1 $keyword $3";
        } else {
            $replacement = '$1' . $keyword . '$3';
        }

        return $replacement;
    }

    /**
     * @param int $maxReplacements - maximum allowed number of replacements
     * @param int $direct - direction
     * @param string $source - initial text
     * @param string $replaceKeyword - this keyword will be replaced
     * @param string $replace -  this text will replace the keyword
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function addLinksToSource($maxReplacements, $direct, $source, $replaceKeyword, $replace)
    {
        $originalReplaceKeyword = $replaceKeyword;
        if ($this->currentNumberOfLinks > $this->getMaxLinkPerPage()) {
            return $source;
        }

        if ($direct == 1) {
            $source = strrev($source);
            $replaceKeyword = strrev($replaceKeyword);
            $replace = strrev($replace);
        }
        $explodeSource = explode($replaceKeyword, $source); // explode text
        $nextSymbol = ['',' ', ',', '.', '!', '?', ')', "\n", "\r", "\r\n"]; // symbols after the word
        $prevSymbol = [',',' ', '(', "\n", "\r", "\r\n"]; // symbols before the word
        $nextTextPatternArray = ['(.*?)&nbsp;$', '(.*?)&lt;span&gt;$'];    // text pattern after the word
        $prevTextPatternArray = ['^&nbsp;(.*?)', '^&lt;\/span&gt;(.*?)']; // text pattern before the word
        $nextPattern = '/' . implode('|', $nextTextPatternArray) . '/';
        $prevPattern = '/' . implode('|', $prevTextPatternArray) . '/';

        $sizeExplodeSource = count($explodeSource);
        $size = 0;
        $prepareSourse = '';

        $replaceNumberOne = false;

        $numberOfReplacements = 0;
        $isStopReplacement = false;

        foreach ($explodeSource as $keySource => $valSource) {
            $size++;

            // maxReplacements for written letters
            if (!$isStopReplacement && ($size < $sizeExplodeSource)
                && ($direct == 0)
                && (((!empty($explodeSource[$keySource + 1][0]))
                        && (in_array($explodeSource[$keySource + 1][0], $nextSymbol)))
                    || (preg_match($prevPattern, $explodeSource[$keySource + 1]))
                    || (empty($explodeSource[$keySource + 1][0])))
                && ((empty($explodeSource[$keySource][strlen($explodeSource[$keySource]) - 1]))
                    || (preg_match($nextPattern, $explodeSource[$keySource]))
                    || ((!empty($explodeSource[$keySource][strlen($explodeSource[$keySource]) - 1]))
                        && (in_array($explodeSource[$keySource][strlen($explodeSource[$keySource]) - 1], $prevSymbol))))
                && ($this->_sizeExplode < $maxReplacements)
                && !$replaceNumberOne) {
                $prepareSourse .= $valSource.$replace;
                $this->_sizeExplode++;
                $replaceNumberOne = true;
                $numberOfReplacements++;
            } elseif (!$isStopReplacement && ($size < $sizeExplodeSource)
                && ($direct == 1)
                && (((!empty($explodeSource[$keySource][strlen($explodeSource[$keySource]) - 1]))
                        && (in_array($explodeSource[$keySource][strlen($explodeSource[$keySource]) - 1], $nextSymbol)))
                    || (preg_match($prevPattern, $explodeSource[$keySource]))
                    || (empty($explodeSource[$keySource][strlen($explodeSource[$keySource]) - 1])))
                && ((empty($explodeSource[$keySource + 1][0]))
                    || (preg_match($nextPattern, $explodeSource[$keySource + 1]))
                    || ((!empty($explodeSource[$keySource + 1][0]))
                        && (in_array($explodeSource[$keySource + 1][0], $prevSymbol))))
                && ($this->_sizeExplode < $maxReplacements)
                && !$replaceNumberOne) {
                $prepareSourse .= $valSource.$replace;
                $this->_sizeExplode++;
                $replaceNumberOne = true;
                $numberOfReplacements++;
            } elseif ($size < $sizeExplodeSource) {
                $prepareSourse .= $valSource.$replaceKeyword;
            } else {
                $prepareSourse .= $valSource;
            }

            if ($this->currentNumberOfLinks +  $numberOfReplacements == $this->getMaxLinkPerPage()) {
                $isStopReplacement = true;
            }
        }

        //to use maxReplacements  the desired number of times
        $this->addReplacementCount($originalReplaceKeyword, $numberOfReplacements);
        $this->currentNumberOfLinks = $this->currentNumberOfLinks +  $numberOfReplacements;

        if ($direct == 1) {
            $prepareSourse = strrev($prepareSourse);
        }
        // if we use $maxReplacements is set, we may replace the same keyword with several calls if this functions
        // that's why we have to sum numbers.
        $this->debug['current_link']->setActualNumberOfReplacements(
            $this->debug['current_link']->getActualNumberOfReplacements() + $numberOfReplacements
        );
        return $prepareSourse;
    }

    /**
     * Get number of already done replacements for word on the page globally
     *
     * @param string $keyword
     * @return int
     */
    protected function getRelpacementCount($keyword)
    {
        if (!isset($this->_replacementsCountGlobal[strtolower($keyword)])) {
            $this->_replacementsCountGlobal[strtolower($keyword)] = 0;
        }
        return $this->_replacementsCountGlobal[strtolower($keyword)];
    }

    /**
     * Increase number of already done replacements for word on the page globally
     *
     * @param string $keyword
     * @param int $cnt
     * @return void
     */
    protected function addReplacementCount($keyword, $cnt)
    {
        if (!isset($this->_replacementsCountGlobal[strtolower($keyword)])) {
            $this->_replacementsCountGlobal[strtolower($keyword)] = 0;
        }
        $this->_replacementsCountGlobal[strtolower($keyword)] += $cnt;
    }

    /**
     * Split text to array to create the sql query
     *
     * @param string $text
     * @return array
     */
    public function splitText($text)
    {
        $maxTextSymbols = 1000; //number of characters for split the text
        $numberReturnWords = 5;      //number of words which will in every part of the split text
        $textSymbolsCount = iconv_strlen($text);
        if ($textSymbolsCount > $maxTextSymbols) {
            $selectNumber = ceil($textSymbolsCount / $maxTextSymbols);
        }

        $textArrayWithMaxSymbols = [];
        if (isset($selectNumber)) {
            $textArray = str_split($text, $maxTextSymbols);
            foreach ($textArray as $textKey => $textVal) {
                if ($textKey == 0) {
                    $keyBefore = $textKey;
                    $textArrayWithMaxSymbols[$textKey] = $textVal;
                } else {
                    $currentText = explode(' ', $textVal, $numberReturnWords);
                    if (count($currentText) == $numberReturnWords) {
                        $currentTextShift = $currentText;
                        array_shift($currentTextShift);
                        $textArrayWithMaxSymbols[$textKey] = implode(' ', $currentTextShift);
                        $currentTextPop = $currentText;
                        array_pop($currentTextPop);
                        $textArrayWithMaxSymbols[$keyBefore] .=  implode(' ', $currentTextPop);
                        $keyBefore = $textKey;
                    } else {
                        $textArrayWithMaxSymbols[$textKey] = implode(' ', $currentText);
                    }
                }
            }
        }

        if (empty($textArrayWithMaxSymbols)) {
            $textArrayWithMaxSymbols[] = $text;
        }

        return $textArrayWithMaxSymbols;
    }

    /**
     * @return bool
     */
    public function checkSkipLinks()
    {
        if ($this->_isSkipLinks === false) {
            return false;
        }
        if (!$skipLinks = $this->registry->registry('skip_auto_links')) {
            $skipLinks = $this->getConfig()->getSkipLinks($this->storeManager->getStore()->getStoreId());
            if ($skipLinks) {
                $this->registry->register('skip_auto_links', $skipLinks);
            } else {
                $this->_isSkipLinks = false;
            }
        }
        if ($this->seoAutolinkPattern->checkArrayPattern(
            parse_url($this->context->getUrlBuilder()->getCurrentUrl(), PHP_URL_PATH),
            $skipLinks
        )
        ) {
            $this->_isSkipLinks = true;

            return true;
        }

        $this->_isSkipLinks = false;

        return false;
    }

    /**
     * @return array|bool
     */
    public function getExcludedAutoTags()
    {
        if (!$this->registry->registry('excluded_auto_links_tags') && $this->_isExcludedTags) {
            $excludedTags = $this->getConfig()->getExcludedTags($this->getStoreId());
            if ($excludedTags) {
                $this->registry->register('excluded_auto_links_tags', $excludedTags);
            } else {
                $this->_isExcludedTags = false;
            }
        } elseif ($this->_isExcludedTags) {
            $excludedTags = $this->registry->registry('excluded_auto_links_tags');
        }

        $patternsForExclude = [];
        if (isset($excludedTags)) {
            foreach ($excludedTags as $tag) {
                $tag = str_replace(' ', '', $tag);
                $patternsForExclude[] = '#'.'<'.$tag.'[^>]*>.*?</'.$tag.'>'.'#si';
            }

            return $patternsForExclude;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getDebugMessage()
    {
        if (!isset($_GET['debug_autolinks'])) {
            return '';
        }
        $html = [];
        $html[] = "<div style='color:red'>";
        $html[] = "Links limit per page: ". ($this->getMaxLinkPerPage()?$this->getMaxLinkPerPage():"unlimited")."</br>";
        foreach ($this->debug['links'] as $link) {
            /** @var Link $link */
            $html[] = " - ".$link->getKeyword()." (#: ".
                (int)$link->getActualNumberOfReplacements()
                .", Max #: ".($link->getMaxReplacements()?(int)$link->getMaxReplacements():"unlimited").")<br>";
        }
        $html[] = "</div>";

        return implode("\n", $html);
    }
}
