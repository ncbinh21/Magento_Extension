<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\InstagramConnect\Helper;

/**
 * Class Constant
 * @package Yosto\InstagramConnect\Helper
 */
class Constant
{
    //Instagram URL
    const API_URL = 'https://api.instagram.com/v1/';
    const API_OAUTH_URL = 'https://api.instagram.com/oauth/authorize';
    const API_OAUTH_TOKEN_URL = 'https://api.instagram.com/oauth/access_token';

    //Instagram Product Attribute
    const INSTAGRAM_HASH_TAG = 'instagram_hash_tag';

    //Instagram Template Table
    const INSTAGRAM_TEMPLATE_TABLE = 'yosto_instagram_template';
    const INSTAGRAM_TEMPLATE_TABLE_COMMENT = 'table for store instagram template';
    const INSTAGRAM_TEMPLATE_ID = 'template_id';
    const INSTAGRAM_TEMPLATE_TABLE_ID_COMMENT = 'id of template';
    const INSTAGRAM_TEMPLATE_TABLE_TITLE = 'title';
    const INSTAGRAM_TEMPLATE_TABLE_TITLE_COMMENT = 'title of template';
    const INSTAGRAM_TEMPLATE_TABLE_DESCRIPTION = 'description';
    const INSTAGRAM_TEMPLATE_TABLE_DESCRIPTION_COMMENT = 'description of template';
    const INSTAGRAM_TEMPLATE_TABLE_STATUS = 'status';
    const INSTAGRAM_TEMPLATE_TABLE_STATUS_COMMENT = 'status of template';
    const INSTAGRAM_TEMPLATE_TABLE_PC_CODE = 'pc_code';
    const INSTAGRAM_TEMPLATE_TABLE_PC_CODE_COMMENT = 'template code to generate the pc UI';
    const INSTAGRAM_TEMPLATE_TABLE_TABLET_CODE = 'tablet_code';
    const INSTAGRAM_TEMPLATE_TABLE_TABLET_CODE_COMMENT = 'template code to generate the tablet UI';
    const INSTAGRAM_TEMPLATE_TABLE_MOBILE_CODE = 'mobile_code';
    const INSTAGRAM_TEMPLATE_TABLE_MOBILE_CODE_COMMENT = 'template code to generate the mobile UI';

    //Resource model
    const TEMPLATE_RESOURCE_MODEL = 'Yosto\InstagramConnect\Model\ResourceModel\Template';

    //Define models
    const TEMPLATE_MODEL = 'Yosto\InstagramConnect\Model\Template';

    //Common property
    const COMMON_ID = 'id';
    const IS_IDENTITY = 'identity';
    const IS_UNSIGNED = 'unsigned';
    const IS_NULLABLE = 'nullable';
    const IS_PRIMARY = 'primary';
    const DEFAULT_PROPERTY = 'default';
    const DB_TYPE = 'type';
    const INNO_DB = 'InnoDB';
    const CHARSET = 'charset';
    const UTF8 = 'utf8';

    //Order directions
    const ORDER_DIRETION_ASC = 'ASC';
    const ORDER_DIRETION_DESC = 'DESC';

    //Query require
    const QUERRY_REQUIRE_LESS_THAN = 'lt';
    const QUERRY_REQUIRE_GREAT_THAN = 'gt';
    const QUERRY_REQUIRE_LESS_THAN_EQUAL = 'lteq';
    const QUERRY_REQUIRE_GREAT_THAN_EQUAL = 'gteq';
    const QUERY_REQUIRE_NOT_EQUAL = 'neq';

    //Date format
    const DEALGROUP_STANDARD_DATE_FORMAT = 'Y-m-d';
    const DEAL_DATE_FORMAT = "M j, Y";

    //Validate return properties
    const VALIDATE_STATUS = 'status';
    const VALIDATE_MESSAGE = 'message';
    const VALIDATE_PARAMS = 'params';
    const VALIDATE_SUCCESS = 'Validate success !';

    //Message
    const TEMPLATE_IS_NOT_EXIST = 'Template is no longer exist';
    const TEMPLATE_SAVE_SUCCESS = 'The template has been saved';
    const TEMPLATE_SAVE_DUPLICATE_TITLE = 'The template title is duplicate with other template which id is : ';
    const TEMPLATE_DELETE_SUCCESS = 'Deleted successfully!';

}