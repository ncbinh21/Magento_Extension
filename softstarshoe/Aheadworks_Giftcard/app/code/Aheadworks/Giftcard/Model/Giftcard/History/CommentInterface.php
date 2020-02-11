<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Giftcard\History;

use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterface as HistoryEntityInterface;

/**
 * Interface CommentInterface
 *
 * @package Aheadworks\Giftcard\Model\Giftcard\History
 */
interface CommentInterface
{
    /**
     * Retrieve comment type
     *
     * @return int
     */
    public function getType();

    /**
     * Retrieve comment label
     *
     * @param [] $arguments
     * @return string
     */
    public function getLabel($arguments = []);

    /**
     * Render comment
     *
     * @param HistoryEntityInterface[] $arguments
     * @param string $label
     * @param bool $renderingUrl
     * @return string
     */
    public function renderComment(
        $arguments,
        $label = null,
        $renderingUrl = false
    );
}
