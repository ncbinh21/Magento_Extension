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
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Api\Data;

interface CanonicalRewriteStoreInterface
{
    const TABLE_NAME = 'mst_seo_canonical_rewrite_store';

    const ID = 'id';
    const CANONICAL_REWRITE_ID = 'canonical_rewrite_id';
    const STORE_ID = 'store_id';

}