<?php
/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 8/1/17
 * Time: 10:54 AM
 */

namespace Forix\ImportHelper\Model\Import\RawData;

/**
 * Interface RowValidatorInterface
 * @package Forix\ImportHelper\Model\Import\RawData
 */
interface ColumnValidatorInterface
{
    const ERROR_INVALID_SCOPE = 'invalidScope';

    const ERROR_INVALID_WEBSITE = 'invalidWebsite';

    const ERROR_INVALID_STORE = 'invalidStore';

    const ERROR_INVALID_ATTR_SET = 'invalidAttrSet';

    const ERROR_INVALID_TYPE = 'invalidType';

    const ERROR_INVALID_FORMAT = 'invalidFormat';

    const ERROR_INVALID_CATEGORY = 'invalidCategory';

    const ERROR_VALUE_IS_REQUIRED = 'isRequired';

    const ERROR_TYPE_CHANGED = 'typeChanged';

    const ERROR_SKU_IS_EMPTY = 'skuEmpty';

    const ERROR_NO_DEFAULT_ROW = 'noDefaultRow';

    const ERROR_CHANGE_TYPE = 'changeProductType';

    const ERROR_DUPLICATE_SCOPE = 'duplicateScope';

    const ERROR_DUPLICATE_SKU = 'duplicateSKU';

    const ERROR_DUPLICATE_UNIQUE_ATTRIBUTE = 'duplicatedUniqueAttribute';

    const ERROR_MEDIA_URL_NOT_ACCESSIBLE = 'mediaUrlNotAvailable';

    const ERROR_MEDIA_PATH_NOT_ACCESSIBLE = 'mediaPathNotAvailable';

    const ERROR_DUPLICATE_URL_KEY = 'duplicatedUrlKey';

    const ERROR_EXCEEDED_MAX_LENGTH = 'exceededMaxLength';

    const ERROR_COLUMN_EMPTY = 'columnEmpty';

    const ERROR_MEDIA_NOT_FOUND = 'mediaFileNotFound';

    const ERROR_INVALID_ATTRIBUTE_OPTION = 'absentAttributeOption';

    const ERROR_INVALID_ATTRIBUTE_TYPE = 'invalidAttributeType';
    /**
     * Value that means all entities (e.g. websites, groups etc.)
     */
    const VALUE_ALL = 'all';

    public function setColumnName($column);

    public function customValue($value, $rowData);

    public function init($context);

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value ;
     * @param  array $rowData ;
     * @return boolean
     */
    public function isValid($value, $rowData);
}