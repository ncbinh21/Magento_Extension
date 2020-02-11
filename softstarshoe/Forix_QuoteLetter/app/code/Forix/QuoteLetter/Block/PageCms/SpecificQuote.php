<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2 - SoftStart Shoes
 * Date: 06/03/2018
 */

namespace Forix\QuoteLetter\Block\PageCms;

use Magento\Framework\View\Element\Template;

class SpecificQuote extends \Magento\Framework\View\Element\Template
{

    protected $_template = 'Forix_QuoteLetter::pagecms/specificquote.phtml';

    /**
     * @var \Forix\QuoteLetter\Model\ResourceModel\QuoteLetter\CollectionFactory
     */
    protected $collectionFactoryQuote;

    /**
     * SpecificQuote constructor.
     * @param Template\Context $context
     * @param \Forix\QuoteLetter\Model\ResourceModel\QuoteLetter\CollectionFactory $collectionFactoryQuote
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Forix\QuoteLetter\Model\ResourceModel\QuoteLetter\CollectionFactory $collectionFactoryQuote,
        array $data = []
    ) {
        $this->collectionFactoryQuote = $collectionFactoryQuote;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getQuote()
    {
         $collection = $this->collectionFactoryQuote->create()
            ->addFieldToFilter('active_cms', ['eq' => 1]);
        return $collection;
    }
}