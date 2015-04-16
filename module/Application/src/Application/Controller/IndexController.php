<?php

namespace Application\Controller;

use Application\Entity\Categories;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $view = new ViewModel();
        $adsView = new ViewModel();

        $adsView->setTemplate('ads/ads/index');
        $view->addChild($adsView, 'ads');

        return $view;
    }
}
