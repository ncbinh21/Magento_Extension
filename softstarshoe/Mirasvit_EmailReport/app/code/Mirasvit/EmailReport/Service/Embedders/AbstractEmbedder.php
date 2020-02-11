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
 * @package   mirasvit/module-email-report
 * @version   1.0.5
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Service\Embedders;


use Magento\Framework\UrlInterface;
use Mirasvit\Email\Api\Data\QueueInterface;
use Mirasvit\EmailReport\Api\Service\EmbedderInterface;
use Mirasvit\EmailReport\Api\Service\LinkEmbedderInterface;
use Mirasvit\EmailReport\Api\Service\ParamProviderInterface;

/**
 * Class AbstractEmbedder
 * @package Mirasvit\EmailReport\Service\Embedders
 */
abstract class AbstractEmbedder implements EmbedderInterface
{
    /**
     * @var LinkEmbedderInterface
     */
    protected $linkEmbedder;
    /**
     * @var array
     */
    protected $paramProviders;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * ClickEmbedder constructor.
     *
     * @param UrlInterface             $urlBuilder
     * @param LinkEmbedderInterface    $linkEmbedder
     * @param ParamProviderInterface[] $paramProviders
     */
    public function __construct(
        UrlInterface $urlBuilder,
        LinkEmbedderInterface $linkEmbedder,
        $paramProviders = []
    ) {
        $this->linkEmbedder = $linkEmbedder;
        $this->paramProviders = $paramProviders;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve params from passed param providers.
     *
     * @param QueueInterface $queue
     *
     * @return array
     */
    protected function getParams(QueueInterface $queue)
    {
        $params = [];
        foreach ($this->paramProviders as $provider) {
            $params = array_merge($params, $provider->getParams($queue));
        }

        return $params;
    }
}