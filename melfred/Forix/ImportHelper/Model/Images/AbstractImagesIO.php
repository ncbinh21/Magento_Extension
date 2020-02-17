<?php

/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 7/31/17
 * Time: 1:49 PM
 */
namespace Forix\ImportHelper\Model\Images;
abstract class AbstractImagesIO implements InterfaceImagesIO
{
    public function init()
    {
        return $this;
    }

    public function isExistsImage($imageName){
        return file_exists($this->getImageRealPath($imageName));
    }
}