<?php

return array(
    'acl' => array(
        'checkBelong' => array(
            'ads' => array(
                'entity' => 'Application\Entity\Ads',
                'field' => 'id',
                'user_field' => 'userid'
            ),
        ),
        'roles' => array(
            'guest' => array(
                'user' => array(
                    'admin' => array()
                )
            )
        ),
        'permissions' => array(
            'admin' => array(
                'Admin\Controller\Admin' => null,
                'Admin\Controller\Ads' => null,
                'Admin\Controller\Category' => null,
                'Admin\Controller\Users' => null,
                'FormBuilder\Controller\FormBuilder' => null
            ),
            'user' => array(
                'Ads\Controller\AdsForm' => null,
                'Ads\Controller\UserAds' => null,
                'FormBuilder\Controller\FormBuilder' => array('getForm')
            ),
            'guest' => array(
                'Ads\Controller\Ads' => null,
                'User\Controller\User' => null,
                'User\Controller\Captcha' => null,
                'Ads\Controller\AdsForm' => array('getCities', 'getRegions', 'getCategories', 'getRootCategories'),
            )
        )
    )
);