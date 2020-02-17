<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 08/11/2018
 * Time: 14:26
 */

namespace Forix\Payment\Model;


abstract class AbstractAdapter implements ServiceAdapterInterface
{
    protected $_options = ['options' => []];

    /**
     * @var bool
     */
    protected $_hasError = false;

    /**
     * @var string[]
     */
    protected $_messages = [];


    /**
     * @param array $messages
     */
    public function addMessages(array $messages)
    {
        $this->_messages = array_merge($this->_messages, $messages);
    }

    /**
     * @return mixed|string[]
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * @return mixed|void
     */
    public function clearMessage()
    {
        $this->_messages = [];
    }


    /**
     * @return bool|mixed
     */
    public function hasError()
    {
        return $this->_hasError;
    }
}