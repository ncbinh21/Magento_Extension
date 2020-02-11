<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Import\Exception;

use Aheadworks\Giftcard\Api\Exception\ImportValidatorExceptionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ImportValidatorException
 *
 * @package Aheadworks\Giftcard\Model\Import\Exception
 */
class ImportValidatorException extends LocalizedException implements ImportValidatorExceptionInterface
{
}
