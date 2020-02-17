<?php
/**
 * Created by PhpStorm.
 * User: magenest
 * Date: 05/03/2017
 * Time: 16:52
 */

namespace Magenest\SagepayUS\Controller\Checkout;

use Magenest\SagepayUS\Controller\Checkout;

class Response extends Checkout
{
    public function execute()
    {
        $this->logger->debug("Response : ");
        $this->logger->debug(var_export($this->getRequest()->getParams(), true));
    }
}
