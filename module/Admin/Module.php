<?php
namespace Admin;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function init(\Zend\ModuleManager\ModuleManager $moduleManager)
    {
        $sharedEvents = $moduleManager
            ->getEventManager()->getSharedManager();
        $sharedEvents->attach(
            __NAMESPACE__,
            'dispatch',
            function ($e) {
                $controller = $e->getTarget();
                $controller->layout('admin/layout/admin');
            },
            100
        );
    }
}
