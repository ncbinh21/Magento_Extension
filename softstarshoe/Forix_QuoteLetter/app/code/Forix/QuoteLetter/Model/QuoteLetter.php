<?php
/**
 * Hidro Forix Webdesign. 
 * Copyright (C) 2017  Hidro Le
 * 
 * This file included in Forix/QuoteLetter is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Forix\QuoteLetter\Model;

use Forix\QuoteLetter\Api\Data\QuoteLetterInterface;

class QuoteLetter extends \Magento\Framework\Model\AbstractModel implements QuoteLetterInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Forix\QuoteLetter\Model\ResourceModel\QuoteLetter');
    }

    /**
     * Get quoteletter_id
     * @return string
     */
    public function getQuoteletterId()
    {
        return $this->getData(self::QUOTELETTER_ID);
    }

    /**
     * Set quoteletter_id
     * @param string $quoteletterId
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setQuoteletterId($quoteletterId)
    {
        return $this->setData(self::QUOTELETTER_ID, $quoteletterId);
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get position
     * @return string
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * Set position
     * @param string $position
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * Get comment
     * @return string
     */
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * Set comment
     * @param string $comment
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * Get address
     * @return string
     */
    public function getAddress()
    {
        return $this->getData(self::ADDRESS);
    }

    /**
     * Set address
     * @param string $address
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * Get quote_letter_category
     * @return array
     */
    public function getCategoryIds()
    {
        if(!$this->getData('category_ids')){
            $this->setData('category_ids', $this->getResource()->getCategoryIds($this->getId()));
        }
        return $this->getData('category_ids');
    }

    /**
     * Set quote_letter_category_ids
     * @param mixed $quote_letter_category_ids
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setCategoryIds($quote_letter_category_ids)
    {
        return $this->setData('category_ids', $quote_letter_category_ids);
    }

    /**
     * Get quote_letter_product
     * @return array
     */
    public function getProductSKUs()
    {
        if(!$this->getData('product_skus')){
            $this->setData('product_skus',$this->getResource()->getProductSKUs($this->getId()));
        }
        return $this->getData('product_skus');
    }

    /**
     * Set quote_letter_product
     * @param mixed $quote_letter_product_skus
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setProductSKUs($quote_letter_product_skus)
    {
        return $this->setData('product_skus', $quote_letter_product_skus);
    }
}
