<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 07/11/2018
 * Time: 15:45
 */

namespace Forix\Payment\Model;


interface ServiceAdapterInterface
{
    /**
     * @return mixed
     */
    public function getMessages();

    /**
     * @return mixed
     */
    public function clearMessage();

    /**
     * @return mixed
     */
    public function hasError();

    /**
     * @param array $messages
     * @return mixed
     */
    public function addMessages(array $messages);

    /**
     * @param $functionName
     * @param $data = []
     * @return mixed
     */
    public function __call($functionName, $data = []);

    /**
     * @param $serviceUrl
     * @param $config []
     */
    public function init($serviceUrl, $config = []);
}