<?php

namespace Onekb\ImportBot\Deconstruct\Interfaces;

interface SheetInterface
{
    public function getName();

    public function getRowCount();

    public function getColumnCount();

    public function getColumn($column);

    public function getRow($row);

    public function getCell($row, $column);

    public function getRawData();

    public function getTitles();

    public function getData();

}