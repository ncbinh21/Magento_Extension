<?php

/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 7/31/17
 * Time: 1:49 PM
 */
namespace Forix\ImportHelper\Model\Images;
interface InterfaceImagesIO{
    /**
     * @return InterfaceImagesIO
     */
    public function init();
    public function isExistsImage($imageName);
    public function getImageRealPath($imageName);
}