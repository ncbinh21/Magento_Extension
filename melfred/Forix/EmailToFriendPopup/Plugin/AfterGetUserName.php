<?php
/**
 * Date: 5/21/18
 * Time: 12:50 PM
 */

namespace Forix\EmailToFriendPopup\Plugin;


class AfterGetUserName
{

    public function afterGetUserName(\Magento\SendFriend\Block\Send $subject, $result){
        return str_replace('  ',' ', $result);
    }
}