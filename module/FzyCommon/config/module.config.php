<?php
namespace FzyCommon;
use Aws\S3\S3Client;
use Doctrine\Common\Cache\ArrayCache;
use FzyCommon\Service\Base;

return array(
	'service_manager' => array(
		'invokables' => array(
			'FzyCommon\Service\EntityToForm' => 'FzyCommon\Service\EntityToForm',
			'FzyCommon\Service\Flattener' => 'FzyCommon\Service\Flattener',
			'FzyCommon\Service\Search\Result' => 'FzyCommon\Service\Search\Result',
		),
		'factories' => array(
			'FzyCommon\Config' => function($sm) {
				return \FzyCommon\Util\Params::create($sm->get('config'));
			},
			'FzyCommon\ModuleConfig' => function($sm) {
				return $sm->get('FzyCommon\Config')->getWrapped(Base::MODULE_CONFIG_KEY);
			},
			'FzyCommon\Service\Aws\Config' => function($sm) {
				return $sm->get('FzyCommon\ModuleConfig')->getWrapped('aws');
			},
			/**
			 * @return \FzyCommon\Util\Params
			 */
			'FzyCommon\Service\Aws\S3\Config' => function($sm) {
				return $sm->get('FzyCommon\Service\Aws\Config')->getWrapped('s3');
			},
			/**
			 * @return \Aws\S3\S3Client
			 */
			'FzyCommon\Service\Aws\S3' => function($sm) {
				return S3Client::factory($sm->get('FzyCommon\Service\Aws\S3\Config')->get());
			},
			'FzyCommon\Factory\DoctrineCache' => function($sm) {
				/* @var $config \FzyCommon\Util\Params */
				$config      = $sm->get( 'FzyCommon\ModuleConfig' );
				if ($config->get('production') && class_exists('Redis')) {
					try {
						$redisConfig = $config->getWrapped( 'doctrine_cache_config' );
						$cache       = new \Doctrine\Common\Cache\RedisCache();
						$redis       = new \Redis();
						$redis->connect( $redisConfig->get( 'host', '127.0.0.1' ), $redisConfig->get( 'port', 6379 ),
							$redisConfig->get( 'timeout', 5 ) );
						$cache->setRedis( $redis );

						return $cache;
					} catch ( \Exception $e ) {
						if ( $config->get( 'debug' ) ) {
							throw $e;
						}
					}
				}
				return new ArrayCache();
			},
			'doctrine.cache.fzy_cache' => function($sm) {
				/* @var $config \FzyCommon\Util\Params */
				$config = $sm->get('FzyCommon\ModuleConfig');
				return $sm->get($config->get('doctrine_cache', 'FzyCommon\Factory\DoctrineCache'));
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
		),
		'configuration' => array(
			'orm_default' => array(
				'generate_proxies' => false,
				'metadata_cache' => 'fzy_cache',
			),
		),
	),
	Base::MODULE_CONFIG_KEY => array(
		'debug' => false,
		'production' => true,
		'doctrine_cache' => 'FzyCommon\Factory\DoctrineCache',
		'doctrine_cache_config' => array(
			'host' => '127.0.0.1',
			'port' => 6379,
			'timeout' => 5,
		),
	),
);
