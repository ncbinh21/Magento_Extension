<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * CodeGenerationSettingsInterface
 * @api
 */
interface CodeGenerationSettingsInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const QTY = 'qty';
    const LENGTH = 'length';
    const FORMAT = 'format';
    const PREFIX = 'prefix';
    const SUFFIX = 'suffix';
    const DELIMITER_AT_EVERY = 'delimiter_at_every';
    const DELIMITER = 'delimiter';
    /**#@-*/

    /**
     * Get qty
     *
     * @return int
     */
    public function getQty();

    /**
     * Set qty
     *
     * @param int $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * Get code length
     *
     * @return int
     */
    public function getLength();

    /**
     * Set code length
     *
     * @param int $length
     * @return $this
     */
    public function setLength($length);

    /**
     * Get code format
     *
     * @return string
     */
    public function getFormat();

    /**
     * Set code format
     *
     * @param string $format
     * @return $this
     */
    public function setFormat($format);

    /**
     * Get code prefix
     *
     * @return string|null
     */
    public function getPrefix();

    /**
     * Set code prefix
     *
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix);

    /**
     * Get code suffix
     *
     * @return string|null
     */
    public function getSuffix();

    /**
     * Set code suffix
     *
     * @param string $suffix
     * @return $this
     */
    public function setSuffix($suffix);

    /**
     * Get delimiter at every X characters
     *
     * @return int|null
     */
    public function getDelimiterAtEvery();

    /**
     * Set delimiter at every X characters
     *
     * @param int $delimiterAtEvery
     * @return $this
     */
    public function setDelimiterAtEvery($delimiterAtEvery);

    /**
     * Get delimiter
     *
     * @return string|null
     */
    public function getDelimiter();

    /**
     * Set delimiter
     *
     * @param string $delimiter
     * @return $this
     */
    public function setDelimiter($delimiter);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsExtensionInterface $extensionAttributes
    );
}
