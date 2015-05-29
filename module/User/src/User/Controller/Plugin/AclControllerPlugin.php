<?php

namespace User\Controller\Plugin;

use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\Permissions\Acl\Acl,
    Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Mvc\MvcEvent;

class AclControllerPlugin extends AbstractPlugin
{
    private $auth;
    private $user;
    private $identityConf;
    private $isAuth = false;

    public function __construct(AuthenticationService $auth)
    {
        $this->auth = $auth;
        $this->user = $this->auth->getStorage()->read();
    }

    public function doAuthorization(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();

        $acl = new Acl();
        $config = $sm->get('config')['acl'];
        $this->identityConf = $config['checkBelong'];
        $action = $e->getRouteMatch()->getParam('action');
        $controller = $e->getRouteMatch()->getParam('controller');

        $role = !empty($this->user) ? $this->user->getRole() : 'guest';
        $roleConf = $config['roles'];
        $roleParent = null;


        $closure = function ($children) use (&$closure, $acl, &$roleParent) {
            foreach ($children as $key => $val) {
                if (!empty($roleParent)) {
                    $acl->addRole(new Role($key), $roleParent);
                } else {
                    $acl->addRole(new Role($key));
                }

                $roleParent = $key;

                if (!empty($val)) {
                    if (is_array($val)) {
                        $closure($val);
                    } else {
                        $acl->addRole(new Role($val), $roleParent);
                    }
                }
            }
        };

        $closure($roleConf);

        foreach ($config['permissions'] as $key => $val) {
            $resources = null;

            if (!empty($val)) {
                $resources = array_keys($val);

                foreach ($resources as $resource) {
                    if (!$acl->hasResource($resource)) {
                        $acl->addResource($resource);
                    }
                }
            }

            if (!empty($resources)) {
                foreach ($resources as $res) {
                    $acl->allow($key, $res, $val[$res]);
                }
            } else {
                $acl->allow($key, null, null);
            }
        }

        if (!$acl->hasResource($controller) ||
            !$acl->isAllowed($role, $controller, $action)
        ) {
            $router = $e->getRouter();

            if ($role === 'guest') {
                $url = $router->assemble(array(), array('name' => 'login'));
            } else {
                $url = $router->assemble(array(), array('name' => 'home'));
            }

            $response = $e->getResponse();
            $response->setStatusCode(302);
            $response->getHeaders()->addHeaderLine('Location', $url);
            $e->stopPropagation();
        } elseif ($role !== 'guest') {
            $this->isAuth = true;
        }
    }

    public function belongsToUser($type, $entityId)
    {
        if ($this->isAuth) {
            if (array_key_exists($type, $this->identityConf)) {
                $conf = $this->identityConf[$type];
                $em = $this->getController()->getEntityManager();

                $match = $em->getRepository($conf['entity'])
                    ->findOneBy(array(
                        $conf['field'] => $entityId,
                        $conf['user_field'] => $this->user
                    ));

                if (is_object($match)) {
                    return true;
                }
            }
        }

        return false;
    }

}