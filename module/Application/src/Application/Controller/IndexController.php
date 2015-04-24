<?php

namespace Application\Controller;

use Application\Entity\Categories;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $ads = $this->forward()->dispatch('Ads\Controller\Ads', array('action' => 'mainCategories'));

        return $ads;
    }
}
