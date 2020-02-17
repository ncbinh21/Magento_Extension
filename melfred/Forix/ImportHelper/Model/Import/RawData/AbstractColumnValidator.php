<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 20/07/2018
 * Time: 18:13
 */

namespace Forix\ImportHelper\Model\Import\RawData;


abstract class AbstractColumnValidator implements \Forix\ImportHelper\Model\Import\RawData\ColumnValidatorInterface
{
    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = [];

    /**
     * Get validation failure messages
     *
     * @return string[]
     * @api
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Whether it has failure messages
     *
     * @return bool
     * @api
     */
    public function hasMessages()
    {
        return !empty($this->_messages);
    }

    /**
     * Clear messages
     *
     * @return void
     */
    protected function _clearMessages()
    {
        $this->_messages = [];
    }

    /**
     * Add messages
     *
     * @param array $messages
     * @return void
     */
    protected function _addMessages(array $messages)
    {
        $this->_messages = array_merge_recursive($this->_messages, $messages);
    }
}