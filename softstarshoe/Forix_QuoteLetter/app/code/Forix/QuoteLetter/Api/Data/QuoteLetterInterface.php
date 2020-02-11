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

namespace Forix\QuoteLetter\Api\Data;

interface QuoteLetterInterface
{

    const QUOTELETTER_ID = 'quoteletter_id';
    const ADDRESS = 'address';
    const POSITION = 'position';
    const NAME = 'name';
    const COMMENT = 'comment';


    /**
     * Get quoteletter_id
     * @return string|null
     */
    public function getQuoteletterId();

    /**
     * Set quoteletter_id
     * @param string $quoteletter_id
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setQuoteletterId($quoteletterId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setName($name);

    /**
     * Get position
     * @return string|null
     */
    public function getPosition();

    /**
     * Set position
     * @param string $position
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setPosition($position);

    /**
     * Get comment
     * @return string|null
     */
    public function getComment();

    /**
     * Set comment
     * @param string $comment
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setComment($comment);

    /**
     * Get address
     * @return string|null
     */
    public function getAddress();

    /**
     * Set address
     * @param string $address
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setAddress($address);

    /**
     * Get quote_letter_category_ids
     * @return array|null
     */
    public function getCategoryIds();

    /**
     * Set quote_letter_category_ids
     * @param mixed $quote_letter_category_ids
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setCategoryIds($quote_letter_category_ids);

    /**
     * Get quote_letter_product_skus
     * @return array|null
     */
    public function getProductSKUs();

    /**
     * Set quote_letter_product_skus
     * @param array $quote_letter_product_skus
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     */
    public function setProductSKUs($quote_letter_product_skus);
}
