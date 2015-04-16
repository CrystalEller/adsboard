<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 24.02.15
 * Time: 22:19
 */

namespace User\Controller;


use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;

class CaptchaController extends AbstractActionController
{

    public function imageAction()
    {
        $imgId = $this->params('image');
        $response = $this->getResponse();
        $imageContent = file_get_contents('./data/captcha/images/' . $imgId);

        $response
            ->getHeaders()
            ->addHeaderLine('Content-Type', 'image/png');
        $response->setContent($imageContent);

        return $response;
    }
}