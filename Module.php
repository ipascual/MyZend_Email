<?php
namespace Email;

use Zend\Module\Consumer\AutoloaderProvider,
	Zend\EventManager\StaticEventManager,
	Zend\ModuleManager\Feature\AutoloaderProviderInterface,
	Zend\ModuleManager\Feature\ConfigProviderInterface,
	Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\ModuleRouteListener;

class Module implements
AutoloaderProviderInterface, ConfigProviderInterface, ServiceProviderInterface {

    /**
     * 
     * @return array
     */
    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * 
     * @return array
     */
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'email' => function ($sm) {
                    $config = $sm->get('Config');
                    $service = new Service\EmailService($config["email"]);
                    $service->setViewHelperManager($sm->get('viewhelpermanager'));
                    return $service;
                }
            )
        );
    }

    /**
     * 
     * @return array
     */
    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

}
