<?php

namespace Onekb\ImportBot\Deconstruct\Interfaces;

interface FileInterface
{
    public function getFile();

    public function getFileName();

    public function getSheetNames();

    public function getSheet($sheetName): SheetInterface;
}