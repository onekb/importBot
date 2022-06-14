<?php

namespace Onekb\ImportBot;

class Config
{
    protected array $config;

    public static function make($config = [])
    {
        return new self($config);
    }

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function set($config = [])
    {
        $this->config = $config;

        return $this->config;
    }

    public function __get($name)
    {
        if (! isset($this->config[$name])) {
            throw new \Exception("Config {$name} not found");
        }

        return $this->config[$name];
    }
}