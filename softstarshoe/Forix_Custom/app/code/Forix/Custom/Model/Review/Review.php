<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 5/8/2018
 * Time: 2:13 PM
 */
namespace Forix\Custom\Model\Review;

class Review extends \Magento\Review\Model\Review
{
    /**
     * Validate review summary fields
     *
     * @return bool|string[]
     */
    public function validate()
    {
        $errors = [];

        if (!\Zend_Validate::is($this->getNickname(), 'NotEmpty')) {
            $errors[] = __('Please enter a nickname.');
        }

        if (!\Zend_Validate::is($this->getDetail(), 'NotEmpty')) {
            $errors[] = __('Please enter a review.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
}