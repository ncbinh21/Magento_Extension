<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2 - SoftStart Shoes
 */
namespace Forix\Catalog\Block\Cms;

use Magento\Framework\View\Element\Template;

class Title extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $page;

    /**
     * Title constructor.
     * @param \Magento\Cms\Model\Page $page
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Cms\Model\Page $page,
        Template\Context $context,
        array $data = []
    ) {
        $this->page = $page;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Cms\Model\Page
     */
    public function getPage(){
        if ($this->page->getId()) {
            return $this->page;
        }
        return false;
    }
}