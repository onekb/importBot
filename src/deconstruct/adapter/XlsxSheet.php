<?php

namespace Onekb\ImportBot\Deconstruct\Adapter;

use Onekb\ImportBot\Deconstruct\Config;
use Onekb\ImportBot\Deconstruct\Interfaces\SheetInterface;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
        return Coordinate::stringFromColumnIndex(count($this->getTitle()));
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

    public function getTitle()
    {
        $titleLine = $this->config['titleLine'] ?? 1;

        $title = $this->sheet->rangeToArray(
            'A' . $titleLine . ':' . $this->sheet->getHighestColumn() . $titleLine
        )[0];

        // 去除空列
        return array_filter($title, function ($title) {
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

    // 设置单元格内容 根据列（数字）和行
    public function setCellValueByColumnAndRow($column, $row, $value)
    {
        $this->sheet->setCellValueByColumnAndRow($column, $row, $value);
    }

    public function save($fileName = null)
    {
        $fileName = $fileName ?? $this->sheet->getTitle();
        $writer = IOFactory::createWriter($this->sheet->getParent(), 'Xlsx');
        $writer->save($fileName . '.xlsx');
    }

}