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
        $imagePath = './public/' . ltrim($config['img_root'], '/') . '/' . ltrim($imageName, '/');
        $thumbImg = ltrim($config['img_root'], '/') . '/' .
            ltrim($config['thumbs'], '/') . '/' .
            ltrim($imageName, '/');

        if (!file_exists('./public/' . $thumbImg)) {
            $imagick = new \Imagick(realpath($imagePath));
            $imagick->setbackgroundcolor('#ffffff');
            $imagick->thumbnailImage($width, $height, true, true);
            $imagick->writeImage('./public/' . $thumbImg);
        }

        return '/' . $thumbImg;
    }

    public function getConfig()
    {
        return $this->getServiceLocator()->getServiceLocator()->get('Config');
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