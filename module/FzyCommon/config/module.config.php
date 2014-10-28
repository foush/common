<?php
return array(
	'service_manager' => array(
		'invokables' => array(
			'FzyCommon\Service\EntityToForm' => 'FzyCommon\Service\EntityToForm',
			'FzyCommon\Service\Flattener' => 'FzyCommon\Service\Flattener',
		),
		'factories' => array(

		),
		'abstract_factories' => array(

		),
	),
	'controller_plugins' => array(
		'invokables' => array(
			'searchResult' => 'FzyCommon\Controller\Plugin\SearchResult',
			'updateResult' => 'FzyCommon\Controller\Plugin\UpdateResult',
			'e2f'          => 'FzyCommon\Controller\Plugin\EntityToForm',
		)
	),
	'view_helpers' => array(
		'invokables' => array(
			'e2f'            => 'FzyCommon\View\Helper\EntityToForm',
			'ngInit'         => 'FzyCommon\View\Helper\NgInit',
		),
		'factories' => array(
			'flashMessages' => function($sm) {
				$flashmessenger = $sm->getServiceLocator()
				                     ->get('ControllerPluginManager')
				                     ->get('flashmessenger');

				$messages = new \FzyCommon\View\Helper\FlashMessages();
				$messages->setFlashMessenger($flashmessenger);

				return $messages;
			},
		),
	),
);