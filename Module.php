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

	public function getServiceConfig() {
		return array(
			'factories' => array(
				'email' => function ($sm) {
					$config = $sm->get('Config');
					return new Service\EmailService($config["email"]);
				}
			)
		);
	}

	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	public function init($moduleManager) {
		$sharedEvents = $moduleManager->getEventManager()->getSharedManager();
		$sharedEvents->attach(__NAMESPACE__, \Zend\Mvc\MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 100);
	}

	public function preDispatch($event) {
		//Unauthorized request after success login
		$session = $event->getApplication()->getServiceManager()->get('session');
		if ($lastRequest = $session->get("lastRequest")) {
			$event->getTarget()->getRequest()->setMethod($lastRequest["request"]->getMethod());
			$event->getTarget()->getRequest()->setPost($lastRequest["request"]->getPost());
			$event->getTarget()->getRequest()->setQuery($lastRequest["request"]->getQuery());

			//Delete request
			$session->set("lastRequest", null);
		}

		//Easy
		$event->getTarget()->user = $event->getTarget()->authPlugin()->getIdentity();

		//ServiceManager
		$sm = $event->getApplication()->getServiceManager();

		//Services
		//...
		//Helpers
		//...
		//Validator
		//...
		//Vendor Helpers
		$event->getTarget()->facebook = $sm->get('facebook');
		$event->getTarget()->email = $sm->get('email');
		$event->getTarget()->session = $sm->get('session');
	}

}