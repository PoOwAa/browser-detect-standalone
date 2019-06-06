<?php

namespace PoOwAa\BrowserDetect;

use PoOwAa\BrowserDetect\Parser as Parser;

class Browser
{
    protected static $agent;

    protected static $parser = null;

    protected static function getParser()
    {
        if (is_null(self::$parser)) {
            self::$parser = new Parser();
        }

        return self::$parser;
    }

    public static function __callStatic($method, $params)
    {
        self::getParser();
        $result = self::$parser->detect();

        if (method_exists($result, $method)) {
            return call_user_func_array([$result, $method], $params);
        }

        throw new BadMethodCallException(sprintf('%s method does not exists!', $method, ResultInterface::class));
    }
}
