<?php

namespace Onekb\ImportBot\Processing\Adapter;

//use Illuminate\Support\Facades\Validator;
use Onekb\ImportBot\Deconstruct\Interfaces\SheetInterface;
use Onekb\ImportBot\Processing\Interfaces\ValidateInterface;
use Onekb\ImportBot\Validator;

class Validate implements ValidateInterface
{
    public array $rules = [];

    public array $messages = [];

    public $datas = [];

    public $title = [];

    public function validate(SheetInterface $sheet)
    {
        $this->datas = $sheet->getData();
        $this->title = $sheet->getTitle();

        return $this->validateDate();
    }

    public function validateDate()
    {
        // 错误记录
        $errors = [];
        // 循环
        foreach ($this->datas as $key => $value) {
            // 验证
            if ($data = array_combine($this->title, $value)) {
                $validator = Validator::make(
                    $data,
                    $this->rules,
                    $this->messages
                );
                // 如果验证失败
                if ($validator->fails()) {
                    // 记录错误
                    $errors[$key] = $validator->errors()->toArray();
                }
            } else {
                // data和title个数不一致
                $errors[$key] = '存在空缺值';
            }
        }

        if ($errors) {
            return $errors;
        }

        return true;
    }

    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    public function setMessages(array $messages)
    {
        $this->messages = $messages;
    }
}