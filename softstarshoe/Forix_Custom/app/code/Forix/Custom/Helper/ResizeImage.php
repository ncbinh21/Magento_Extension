<?php
/**
 * Created by Johnny.
 * User: Johnny
 * Date: 9/5/2016
 * Time: 2:19 PM
 */
namespace Forix\Custom\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;

class ResizeImage extends AbstractHelper{

    protected $_filesystem;
    protected $_storeManager;
    protected $_directory;
    protected $_imageFactory;


    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory

    ) {
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory;

    }
    public function getThumbnailImage($img_file, $widthCrop, $heightCrop = null, $crop = false)
    {
        if(empty($img_file)){
            return '';
        }
        try {
            $basePath = $this->getImageMediaPath();
            $basePathBefore = $this->getImageMediaPath().'fanphoto/';
            $mediaUrl = $this->getImageMediaUrl();
            $basePathThumbnail = $basePath. "/fanphoto/resize/{$widthCrop}x{$heightCrop}/";
            $thumbnailFile = $basePathThumbnail . $img_file;

            if(is_file($thumbnailFile)){
                return $mediaUrl . "fanphoto/resize/{$widthCrop}x{$heightCrop}/" . $img_file;
            }
            if(!is_file($basePathBefore.$img_file)){
                return '';
            }

            $image = $this->_imageFactory->create();
            $image->open($basePathBefore.$img_file);

            $image->keepAspectRatio(true);
            $image->keepFrame(true);
            $image->keepTransparency(false);
            $image->constrainOnly(true);
            $image->backgroundColor(array(255, 255, 255));
            $image->quality(100);
            $image->getMimeType();

            list($originalWidth, $originalHeight)  = getimagesize($basePathBefore.$img_file);
            $ratioW = $widthCrop/$originalWidth;
            $ratioH = ($heightCrop / $originalHeight );
            //Resize before crop
            if($ratioH == 1 || $ratioW == 1 ){
                $width = $originalWidth;
                $height = $originalHeight;
            }/* elseif ($ratioH >= $ratioW)
            {
                $height = $heightCrop;
                $width = ceil(($height/$originalHeight) * $originalWidth);// max(1, $heightCrop * $ratio);
                echo $width;
            }*/
            else
            {
                $width = $widthCrop;
               // $height = ceil(($width / $originalWidth) * $originalHeight);//max(1, $widthCrop / $ratio);
                $height = $heightCrop;
            }
            $image->resize($width,$height);

            if($crop && !is_null($widthCrop) && !is_null($heightCrop)){
                $image->keepAspectRatio(true);
                $image->keepFrame(true);
                $image->backgroundColor(array(255, 255, 255));
                $targetRatio = $widthCrop/$heightCrop;
                $originalWidth = $width; $originalHeight = $height;
                $ratio = $originalWidth/$originalHeight;
                if($ratio != $targetRatio){

                    $targetWidth = $targetHeight = min($originalWidth, $originalHeight);

                    if ($ratio >= 1) {
                        $targetWidth = floor($targetHeight * $targetRatio);
                    } else {
                        $targetHeight = floor($targetWidth / $targetRatio);
                    }
                    if($targetWidth <= $originalWidth || $targetHeight <= $originalHeight) {
                        $diffHeight = $originalHeight - $heightCrop;
                        $diffWidth = $originalWidth - $widthCrop;
                        $top = floor($diffHeight / 2);
                        $left = floor($diffWidth / 2);
                        $right = ceil($diffWidth / 2);
                        $bottom = ceil($diffHeight / 2);//$diffHeight;

                        $image->crop(
                            $top,
                            $left,
                            $right,
                            $bottom
                        );
                    }
                }
            }

            $image->save($thumbnailFile);
            $img = $mediaUrl . "fanphoto/resize/{$widthCrop}x{$heightCrop}/" . $img_file;
        }catch(\Exception $e) {
            $img = '';
        }
        return $img;
    }
    public function getThumbnailImage2($img_file, $widthCrop, $heightCrop = null, $crop = false)
    {
        if(empty($img_file)){
            return '';
        }
        try {
            $basePath = $this->getImageMediaPath();
            $basePathBefore = $this->getImageMediaPath();
            $mediaUrl = $this->getImageMediaUrl();
            $basePathThumbnail = $basePath. "/resize/{$widthCrop}x{$heightCrop}/";
            $thumbnailFile = $basePathThumbnail . $img_file;
            if(file_exists($thumbnailFile)){
                return $mediaUrl . "resize/{$widthCrop}x{$heightCrop}/" . $img_file;
            }
            if(!file_exists($basePathBefore.$img_file)){
                return '';
            }

            $image = $this->_imageFactory->create();
            $image->open($basePathBefore.$img_file);

            $image->keepAspectRatio(true);
            $image->keepFrame(true);
            $image->keepTransparency(false);
            $image->constrainOnly(true);
            $image->backgroundColor(array(255, 255, 255));
            $image->quality(100);
            $image->getMimeType();

            list($originalWidth, $originalHeight)  = getimagesize($basePathBefore.$img_file);
            $ratioW = $widthCrop/$originalWidth;
            $ratioH = ($heightCrop / $originalHeight );
            //Resize before crop
            if($ratioH == 1 || $ratioW == 1 ){
                $width = $originalWidth;
                $height = $originalHeight;
            } elseif ($ratioH >= $ratioW)
            {
                $height = $heightCrop;
                $width = ceil(($height/$originalHeight) * $originalWidth);// max(1, $heightCrop * $ratio);
                echo $width;
            }
            else
            {
                $width = $widthCrop;
                $height = ceil(($width / $originalWidth) * $originalHeight);//max(1, $widthCrop / $ratio);
            }
            $image->resize($width,$height);

            if($crop && !is_null($widthCrop) && !is_null($heightCrop)){
                $image->keepAspectRatio(true);
                $image->keepFrame(true);
                $image->backgroundColor(array(255, 255, 255));
                $targetRatio = $widthCrop/$heightCrop;
                $originalWidth = $width; $originalHeight = $height;
                $ratio = $originalWidth/$originalHeight;
                if($ratio != $targetRatio){

                    $targetWidth = $targetHeight = min($originalWidth, $originalHeight);

                    if ($ratio >= 1) {
                        $targetWidth = floor($targetHeight * $targetRatio);
                    } else {
                        $targetHeight = floor($targetWidth / $targetRatio);
                    }
                    if($targetWidth <= $originalWidth || $targetHeight <= $originalHeight) {
                        $diffHeight = $originalHeight - $heightCrop;
                        $diffWidth = $originalWidth - $widthCrop;
                        $top = floor($diffHeight / 2);
                        $left = floor($diffWidth / 2);
                        $right = ceil($diffWidth / 2);
                        $bottom = ceil($diffHeight / 2);//$diffHeight;

                        $image->crop(
                            $top,
                            $left,
                            $right,
                            $bottom
                        );
                    }
                }
            }

            $image->save($thumbnailFile);
            $img = $mediaUrl . "resize/{$widthCrop}x{$heightCrop}/" . $img_file;
        }catch( \Exception $e ) {
            $img = '';
        }
        return $img;
    }
    public function getImageMediaUrl()
    {
        return  $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    //Path before resize
    public function getImageMediaPath()
    {
        return  $this->_filesystem
            ->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath();
    }
}