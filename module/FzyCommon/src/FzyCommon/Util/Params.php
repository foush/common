<?php
namespace FzyCommon\Util;

use Zend\Json\Json;
use Zend\Http\Request;

/**
 * Wrapper for passing arrays of parameter values between services
 * Class Param
 * @package FzyCommon\Util
 */
class Params
{
    protected $params = array();

    public function __construct(array $params = null)
    {
        if (!empty($params)) {
            $this->params = $params;
        }
    }

    /**
     * If $key is not specified, will return entire param array
     * If $key is specified, will check the parameter array for key $key and
     * return the value if it exists, otherwise $default
     * @param  null       $key
     * @param  null       $default
     * @return array|null
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->params;
        }

        return $this->has($key) ? $this->params[$key] : $default;
    }

    /**
     * @param $key
     * @param array $default
     *
     * @return Params
     */
    public function getWrapped($key, $default = array())
    {
        return Params::create($this->get($key, $default));
    }

    /**
     * Additionally converts 1, "1", and "true" to true and 0, "0", and "false" to false
     * @param $key
     * @param  null $default
     * @return bool
     */
    public function getBoolean($key, $default = null)
    {
        $value = $this->get($key, $default);
        if (is_numeric($value)) {
            $value = intval($value);
        } elseif (is_string($value)) {
            $value = strtolower($value) == 'true' ? true : false;
        }

        return $value ? true : false;
    }

    /**
     * Set a specific key/value pair
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Remove a specific key/value pair
     * @param $key
     * @param $value
     * @return $this
     */
    public function remove($key)
    {
        unset($this->params[$key]);

        return $this;
    }

    /**
     * Overwrite entire param array
     * @param  array $params
     * @return $this
     */
    public function setAll(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get entire param array
     * @param  array $params
     * @return $this
     */
    public function getAll()
    {
        return $this->params;
    }

    /**
     * Returns whether the key exists in this param array
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasKey($key)
    {
        return array_key_exists($key, $this->params);
    }

    /**
     * Create a Param instance
     * @param  null                      $params
     * @param  null|Request              $request
     * @return Params
     * @throws \InvalidArgumentException
     */
    public static function create($params = null, Request $request = null)
    {
        if ($params === null) {
            return new Params();
        } elseif (is_array($params)) {
            return new Params($params);
        } elseif ($params instanceof \Traversable) {
            return new Params(self::extractParamsFromCollection($params));
        } elseif ($params instanceof \Zend\Mvc\Controller\Plugin\Params) {
            $results = array();
            /* @var $r \Zend\Http\Request */
            foreach (array(
                         $params->fromQuery(),
                         $params->fromPost(),
                         self::getBodyData($request),
                         $params->fromRoute(),
                         $params->fromFiles(),
                     ) as $collection) {
                $results = array_merge($results, $collection);
            }

            return new Params($results);
        } elseif ($params instanceof Param) {
            return new Params($params->get());
        }
        throw new \InvalidArgumentException("Unrecognized parameter collection");
    }

    public function slice(array $keys)
    {
        $slice = array();
        foreach ($keys as $key) {
            $slice[$key] = $this->get($key);
        }

        return $slice;
    }

    public function merge($data)
    {
        if ($data instanceof Params) {
            $this->params = array_merge($this->params, $data->get());
        } elseif (is_array($data)) {
            $this->params = array_merge($this->params, $data);
        } else {
            throw new \RuntimeException("Unable to merge parameter data. Expected array or another Params object.");
        }

        return $this;
    }

    protected static function getBodyData(Request $request = null)
    {
        $result = array();
        if ($request !== null) {
            $content = $request->getContent();
            if (!empty($content)) {
                try {
                    $result = Json::decode($content, Json::TYPE_ARRAY);
                } catch (\RuntimeException $e) {
                }
            }
        }

        return $result;
    }

    /**
     * Converts an iterable collection into a raw array
     * @param $collection
     * @return array
     */
    public static function extractParamsFromCollection($collection)
    {
        $result = array();
        foreach ($collection as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }
}
