<?php

$container = new \Pimple\Container();

// 注册文件系统
$container['filesystem'] = function ($c) {
    return new \League\Flysystem\Filesystem(
        new \League\Flysystem\Local\LocalFilesystemAdapter(__DIR__ . '/../')
    );
};

// 注册验证器
$container[\Hyperf\Validation\Contract\ValidatorFactoryInterface::class] = function ($c) {
    return \Onekb\ImportBot\Validator::getInstance();
};

\Onekb\ImportBot\Di::$container = $container;