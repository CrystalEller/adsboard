<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.05.15
 * Time: 15:58
 */

namespace Ads\View\Helper;


use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

class ThumbHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{

    protected $serviceLocator;

    public function __invoke($imageName, $width = null, $height = null)
    {
        $config = $this->getConfig()['img_thumb'];
        $width = (is_numeric($width) && $width > 0) ? $width : $config['width'];
        $height = (is_numeric($height) && $height > 0) ? $height : $config['height'];
        $imagePath = $config['img_root'] . '/' . ltrim($imageName, '/');
        $thumbPath = $config['thumbs'] . '/' . ltrim($imageName, '/');
        $thumbImg = $this->generateImgName($thumbPath, $width, $height);

        if (!file_exists($thumbImg)) {
            $imagick = new \Imagick(realpath($imagePath));

            if ($imagick->getImageWidth() != $width &&
                $imagick->getImageHeight() != $height
            ) {
                $imagick->setbackgroundcolor('#ffffff');
                $imagick->thumbnailImage($width, $height, true, true);
                $imagick->writeImage($thumbImg);
            }
        }

        return '/' . str_replace($config['clear_from_path'], '', $thumbImg);
    }

    public function getConfig()
    {
        return $this->getServiceLocator()->getServiceLocator()->get('Config');
    }

    public function generateImgName($imgName, $width, $height)
    {
        $name = basename($imgName);
        $pos = mb_strpos($imgName, $name);

        $arr = explode('_', $name);
        $clearName = implode('', array_slice($arr, 1));
        $arr[1] = $width;
        $arr[2] = $height;
        $arr[3] = $clearName;

        return substr_replace($imgName, implode('_', $arr), $pos);
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }


    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}