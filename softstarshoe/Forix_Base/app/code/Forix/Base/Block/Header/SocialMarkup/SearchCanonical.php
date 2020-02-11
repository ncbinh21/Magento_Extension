<?php
namespace Forix\Base\Block\Header\SocialMarkup;

class SearchCanonical extends \Forix\Base\Block\Header\SocialMarkup
{
	/**
	 * SearchCanonical constructor.
	 *
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param array $data
	 */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data
    ) {
        parent::__construct($context, $data);
    }

    protected function getMarkupHtml()
    {
        return $this->getSearchCanonical();
    }

    /**
     * @return string
     */
    protected function getSearchCanonical()
    {
        $url = $this->getUrl('catalogsearch/result/');
	    $html  = "<link rel=\"canonical\" href=\"$url\" />\n";

        return $html;
    }
}
