<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2 - Soft StartShoes
 * Date: 1/30/18
 * Time: 2:43 PM
 */

namespace Forix\QuoteLetter\Block;

use Magento\Framework\View\Element\Template;

abstract class AbstractSpecificQuote extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    protected $_quoteLetterFactory;
    
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Forix\QuoteLetter\Model\QuoteLetterFactory $quoteLetterFactory,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_quoteLetterFactory = $quoteLetterFactory;
    }

    /**
     * @param $collection \Forix\QuoteLetter\Model\ResourceModel\QuoteLetter\Collection
     * @return \Forix\QuoteLetter\Model\ResourceModel\QuoteLetter\Collection
     */
    abstract public function addSourceToFilter($collection);

    /**
     * @return \Magento\Catalog\Model\AbstractModel
     */
    abstract public function getSource();

    /**
     * @param $source \Magento\Catalog\Model\AbstractModel
     */
    public function setSource($source){
        $this->setData('source',$source);
        $this->unsetData('quoteletter_collection');
    }
    /**
     * @return \Forix\QuoteLetter\Model\ResourceModel\QuoteLetter\Collection|null
     */
    public function getQuotes()
    {
        if(!$this->getData('quoteletter_collection')) {
            /**
             * @var $collection \Forix\QuoteLetter\Model\ResourceModel\QuoteLetter\Collection
             */
            $collection = $this->_quoteLetterFactory->create()->getCollection();
            $this->addSourceToFilter($collection);
            $this->setData('quoteletter_collection', $collection);
        }
        return $this->getData('quoteletter_collection');
    }
}
