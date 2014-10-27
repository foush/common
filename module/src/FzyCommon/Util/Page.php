<?php
namespace FzyCommon\Util;

class Page
{
    public static function offset(Params $params, $default = 0)
    {
        $start = self::getFromParam('start', $params, $default);

        return self::getFromParam('offset', $params, $start);
    }

    public static function limit(Params $params, $default = 10)
    {
        $length = self::getFromParam('length', $params, $default);

        return self::getFromParam('limit', $params, $length);
    }

    protected static function getFromParam($key, Params $params, $defaultValue = 0)
    {
        $value = $params->get($key, $defaultValue);

        return intval(!is_numeric($value) ? $defaultValue : $value);
    }
}
