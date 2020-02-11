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



namespace Mirasvit\EmailReport\Service;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\Email\Api\Data\QueueInterface;
use Mirasvit\EmailReport\Api\Service\EmbedderInterface;
use Mirasvit\EmailReport\Api\Service\PreparerInterface;
use Mirasvit\EmailReport\Api\Service\RegistrarInterface;

class Preparer implements PreparerInterface
{
    /**
     * @var array
     */
    private $embedders;
    /**
     * @var array
     */
    private $registrars;

    /**
     * Preparer constructor.
     *
     * @param EmbedderInterface[]         $embedders
     * @param RegistrarInterface[]        $registrars
     */
    public function __construct(
        $embedders = [],
        $registrars = []
    ) {
        $this->embedders = $embedders;
        $this->registrars = $registrars;
    }

    /**
     * {@inheritDoc}
     */
    public function prepare(QueueInterface $queue, $content)
    {
        if ($queue->getId()) {
            foreach ($this->embedders as $embedder) {
                $content = $embedder->embed($queue, $content);
            }

            if ($queue instanceof AbstractModel) {
                foreach ($this->registrars as $registrar) {
                    $registrar->register($queue, $queue->getId());
                }
            }
        }

        return $content;
    }
}
