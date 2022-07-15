<?php

namespace Onekb\ImportBot\Processing\Interfaces;

use Onekb\ImportBot\Deconstruct\Interfaces\SheetInterface;

interface ValidateInterface
{
    public function validate(SheetInterface $sheet);

    public function validateDate();

    public function setRules(array $rules);

    public function setMessages(array $messages);
}