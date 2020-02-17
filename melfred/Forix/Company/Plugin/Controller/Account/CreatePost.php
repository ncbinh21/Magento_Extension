<?php
/**
 * Date: 5/18/18
 * Time: 2:04 PM
 */
namespace Forix\Company\Plugin\Controller\Account;

/**
 * Saperate fullname into firstname and lastname before create company account
 * Class CreatePost
 * @package Forix\Company\Plugin\Controller\Account
 */
class CreatePost
{

    public function beforeExecute(\Magento\Company\Controller\Account\CreatePost $subject)
    {
        $fullName = trim($subject->getRequest()->getParam('fullname'));
        $len = strlen($fullName);
        $pos = strpos($fullName, ' ');
        $end = $len - $pos;
        $firstName = substr($fullName, 0, $pos);
        $lastName = substr($fullName, $pos, $end);
        $params = $subject->getRequest()->getParams();
        $params['firstname'] = trim($firstName);
        $params['lastname'] = trim($lastName);
        $subject->getRequest()->setParams($params);
    }
}