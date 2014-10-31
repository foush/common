<?php
namespace FzyCommon;
return array(
	'service_manager' => array(
		'invokables' => array(
			'FzyCommon\Service\EntityToForm' => 'FzyCommon\Service\EntityToForm',
			'FzyCommon\Service\Flattener' => 'FzyCommon\Service\Flattener',
		),
		'factories' => array(
			'FzyCommon\Config' => function($sm) {
				return \FzyCommon\Util\Params::create($sm->get('config'));
			}
		),
	),
	'controller_plugins' => array(
		'invokables' => array(
			'fzySearchResult' => 'FzyCommon\Controller\Plugin\SearchResult',
			'fzyUpdateResult' => 'FzyCommon\Controller\Plugin\UpdateResult',
			'fzyEntityToForm'          => 'FzyCommon\Controller\Plugin\EntityToForm',
		)
	),
	'view_helpers' => array(
		'invokables' => array(
			'fzyEntityToForm'            => 'FzyCommon\View\Helper\EntityToForm',
			'fzyNgInit'         => 'FzyCommon\View\Helper\NgInit',
			'fzyRequest' => 'FzyCommon\View\Helper\Request',
		),
		'factories' => array(
			'fzyFlashMessages' => function($sm) {
				$flashmessenger = $sm->getServiceLocator()
				                     ->get('ControllerPluginManager')
				                     ->get('flashmessenger');

				$messages = new \FzyCommon\View\Helper\FlashMessages();
				$messages->setFlashMessenger($flashmessenger);

				return $messages;
			},
		),
	),
	'doctrine' => array(
		'driver' => array(
			__NAMESPACE__ . '_driver' => array(
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
			),
			'orm_default' => array(
				'drivers' => array(
					__NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
				)
			)
		)
	)
);
