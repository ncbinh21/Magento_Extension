<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface PoolInterface
 * @api
 */
interface PoolInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const NAME = 'name';
    const CODE_LENGTH = 'code_length';
    const CODE_FORMAT = 'code_format';
    const CODE_PREFIX = 'code_prefix';
    const CODE_SUFFIX = 'code_suffix';
    const CODE_DELIMITER_AT_EVERY = 'code_delimiter_at_every';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get code length
     *
     * @return int
     */
    public function getCodeLength();

    /**
     * Set code length
     *
     * @param int $codeLength
     * @return $this
     */
    public function setCodeLength($codeLength);

    /**
     * Get code format
     *
     * @return string
     */
    public function getCodeFormat();

    /**
     * Set code format
     *
     * @param string $codeFormat
     * @return $this
     */
    public function setCodeFormat($codeFormat);

    /**
     * Get code prefix
     *
     * @return string|null
     */
    public function getCodePrefix();

    /**
     * Set code prefix
     *
     * @param string|null $codePrefix
     * @return $this
     */
    public function setCodePrefix($codePrefix);

    /**
     * Get code suffix
     *
     * @return string|null
     */
    public function getCodeSuffix();

    /**
     * Set code suffix
     *
     * @param string|null $codeSuffix
     * @return $this
     */
    public function setCodeSuffix($codeSuffix);

    /**
     * Get code delimiter at every X characters
     *
     * @return int|null
     */
    public function getCodeDelimiterAtEvery();

    /**
     * Set code delimiter at every X characters
     *
     * @param int|null $codeDelimiterAtEvery
     * @return $this
     */
    public function setCodeDelimiterAtEvery($codeDelimiterAtEvery);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Giftcard\Api\Data\PoolExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Giftcard\Api\Data\PoolExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Giftcard\Api\Data\PoolExtensionInterface $extensionAttributes
    );
}
