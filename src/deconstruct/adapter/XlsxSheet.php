<?php

namespace Onekb\ImportBot\Deconstruct\Adapter;

use Onekb\ImportBot\Deconstruct\Config;
use Onekb\ImportBot\Deconstruct\Interfaces\SheetInterface;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class XlsxSheet implements SheetInterface
{
    protected Worksheet $sheet;
    protected array $config;

    public function __construct($sheet)
    {
        $this->sheet = $sheet;
        $this->config = Config::getConfig($sheet->getTitle());
    }

    public function getName()
    {
        return $this->sheet->getTitle();
    }

    public function getRowCount()
    {
        return $this->sheet->getHighestRow();
    }

    public function getColumnCount()
    {
        return Coordinate::stringFromColumnIndex(count($this->getTitles()));
    }

    public function getColumn($column)
    {
        return $this->sheet->rangeToArray($column . '1:' . $column . $this->getRowCount())[0];
    }

    public function getRow($row)
    {
        return $this->sheet->rangeToArray('A' . $row . ':' . $this->getColumnCount() . $row)[0];
    }

    public function getCell($row, $column)
    {
        return $this->sheet->getCell($column . $row)->getValue();
    }

    public function getRawData(): array
    {
        return $this->sheet->toArray();
    }

    public function getTitles()
    {
        $titleLine = $this->config['titleLine'] ?? 1;

        $titles = $this->sheet->rangeToArray(
            'A' . $titleLine . ':' . $this->sheet->getHighestColumn() . $titleLine
        )[0];

        // 去除空列
        return array_filter($titles, function ($title) {
            return $title !== null;
        });
    }

    // 获取数据
    public function getData()
    {
        $dateLine = $this->config['dataStartLine'] ?? 2;

        return $this->sheet->rangeToArray(
            'A' . $dateLine . ':' . $this->getColumnCount() . $this->getRowCount()
        );
    }

}