<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Api\Data;


interface UnsubscriptionInterface
{
    const TABLE_NAME = 'mst_email_unsubscription';

    const ID         = 'unsubscription_id';
    const EMAIL      = 'email';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
