<?php

namespace Onekb\ImportBot\Deconstruct;

class Config
{
    protected static $config;


    public static function set($config = [])
    {
        self::$config = $config;

        return self::$config;
    }

    public static function getConfig($name)
    {
        if (! isset(self::$config[$name])) {
            throw new \Exception("Config {$name} not found");
        }

        return [
            'name' => $name,
            'titleLine' => self::$config[$name]['titleLine'],
            'dataStartLine' => self::$config[$name]['dataStartLine'],
        ];
    }
}