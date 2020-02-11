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
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Api\Data;

interface CanonicalRewriteInterface
{
    const TABLE_NAME = 'mst_seo_canonical_rewrite';

    const ID = 'canonical_rewrite_id';
    const IS_ACTIVE = 'is_active';
    const CANONICAL = 'canonical';
    const REG_EXPRESSION = 'reg_expression';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const ACTIONS_SERIALIZED = 'actions_serialized';
    const SORT_ORDER = 'sort_order';
    const COMMENTS = 'comments';

    //alias for id
    const ID_ALIAS = 'id';

    //model
    const MODEL = 'canonical_rewrite_model';

    //rule
    const RULE_FORM_NAME = 'seo_canonical_rewrite_form';
    const RULE_FIELDSET_NAME = 'rule_conditions_fieldset';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsActive($value);

    /**
     * @return int
     */
    public function getCanonical();

    /**
     * @param int $value
     * @return $this
     */
    public function setCanonical($value);

    /**
     * @return string
     */
    public function getRegExpression();

    /**
     * @param string $value
     * @return $this
     */
    public function setRegExpression($value);

    /**
     * @return string
     */
    public function getConditionsSerialized();

    /**
     * @param string $value
     * @return $this
     */
    public function setConditionsSerialized($value);

    /**
     * @return string
     */
    public function getActionsSerialized();

    /**
     * @param string $value
     * @return $this
     */
    public function setActionsSerialized($value);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $value
     * @return $this
     */
    public function setSortOrder($value);

    /**
     * @return string
     */
    public function getComments();

    /**
     * @param string $value
     * @return $this
     */
    public function setComments($value);

    /**
     * @return \Mirasvit\Seo\Model\Template\Rule\Condition\Combine
     */
    public function getConditionsInstance();

    /**
     * @return Template\Rule\Action\Collection
     */
    public function getActionsInstance();

    /**
     * Retrieve rule combine conditions model
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditions();

}