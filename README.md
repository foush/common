FzyCommon Module
===

Services
--
* `FzyCommon\Service\EntityToForm`: accepts doctrine entity and returns a ZF2 form class (\Zend\Form\Form) bound to the entity and hydrated with its information.
* `FzyCommon\EntityToForm`: alias for `FzyCommon\Service\EntityToForm`
* `FzyCommon\Service\Search\Result`: accepts a result provider interface and formats the search result in a standard way 
* `FzyCommon\Search\Result`: alias for `FzyCommon\Service\Search\Result`
* `FzyCommon\Service\Url`: service to create URLs from ZF2 route names or from an AWS S3 key
* `FzyCommon\Url`: alias for `FzyCommon\Service\Url`
* `FzyCommon\Service\Render`: service to render a View Model or string path to a view file into HTML
* `FzyCommon\Render`: alias for `FzyCommon\Service\Render`
* `FzyCommon\Config`: returns the ZF2 application's configuration wrapped in a `\FzyCommon\Util\Params` container.
* `FzyCommon\ModuleConfig`: returns config data specific to this module (`\FzyCommon\Service\Base::MODULE_CONFIG_KEY`) wrapped in a `\FzyCommon\Util\Params` container.
* `FzyCommon\Service\Aws\Config`: returns the `aws` key from `FzyCommon\ModuleConfig` wrapped in a `\FzyCommon\Util\Params` container.
* `FzyCommon\Service\Aws\S3\Config`: returns the `s3` key from `FzyCommon\Service\Aws\Config` wrapped in a `\FzyCommon\Util\Params` container.
* `FzyCommon\Service\Aws\S3`: returns configured `\Aws\S3\S3Client` object (using `FzyCommon\Service\Aws\S3\Config`)
* `FzyCommon\Factory\DoctrineCache`: returns configured `Doctrine\Common\Cache` class based on environment.

View Helpers
--
* `fzyFlashMessages`: returns an array of arrays of messages in the flash messenger, indexed by their type.
 e.g. ```[
   "success": [
     "Your settings have been saved"
   ],
   "warning": [],
   "danger": [],
   "info": []
 ]```
* `fzyEntityToForm`: provides shortcut to the `FzyCommon\Service\EntityToForm` service
* `fzyNgInit`: handles json encoding and escaping literals, objects and doctrine entities for injection into an angular scope.
* `fzyRequest`: accessor for the view to query the current request

Controller Plugins
--
* `fzySearchResult`: plugin to standardize the response format for a search. 
* `fzyUpdateResult`: plugin to standardize the response format for an update. 
* `fzyEntityToForm`: provides shortcut to the `FzyCommon\Service\EntityToForm` service 

Controllers
--

Options
--
* `debug`: used as a flag to indicate this code should expose errors and exceptions for debugging.
* `production`: used as a flag to indicate this code is running in a production environment.
* `doctrine_cache`: the service key to be used for generating the doctrine cache object. (default: `FzyCommon\Factory\DoctrineCache` which is a Redis service)
* `doctrine_cache_config`: configuration for setting up the doctrine_cache service. (default: the Redis connection credentials)
* `aws`: configuration for connecting to AWS services (should contain credentials indexed by service like:
 ```
 "aws": [
   "s3": [
     "key": "your-aws-key",
     "secret": "your-aws-secret"
   ]
 ]
 ```
