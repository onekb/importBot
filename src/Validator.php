<?php

namespace Onekb\ImportBot;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

class Validator
{
    /**
     * 创建实例
     *
     * @return Factory
     */
    public static function getInstance()
    {
        static $validator = null;
        if ($validator === null) {
            $translation_path = __DIR__ . '/lang';
            $translation_locale = 'zh_cn';
            $translation_file_loader = new FileLoader(new Filesystem(), $translation_path);
            $translator = new Translator($translation_file_loader, $translation_locale);
            $validator = new Factory($translator);
        }

        return $validator;
    }

    public static function make(
        array $data,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
        return self::getInstance()->make($data, $rules, $messages, $customAttributes);
    }
}