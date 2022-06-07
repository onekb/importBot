<?php

$container = new \Pimple\Container();

// 注册文件系统
$container['filesystem'] = function ($c) {
    return new \League\Flysystem\Filesystem(
        new \League\Flysystem\Local\LocalFilesystemAdapter(__DIR__ . '/../')
    );
};

\Onekb\ImportBot\Di::$container = $container;