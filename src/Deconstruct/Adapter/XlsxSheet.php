<?php

namespace Onekb\ImportBot\Deconstruct\Adapter;

use Onekb\ImportBot\Config;
use Onekb\ImportBot\Deconstruct\Interfaces\SheetInterface;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class XlsxSheet implements SheetInterface
{
    protected Worksheet $sheet;
    protected Config $config;

    public function __construct($sheet, $config)
    {
        $this->sheet = $sheet;
        $this->config = $config;
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
        $titleLine = $this->config->title_line ?? 1;

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
        $dateLine = $this->config->data_start_line ?? 2;
        $datas = [];
        $columnCount = $this->getColumnCount();
        for ($i = $dateLine; $i <= $this->getRowCount(); $i++) {
            $data = $this->sheet->rangeToArray('A' . $i . ':' . $columnCount . $i)[0];
            if (join('', $data)) {
                $datas[] = $data;
            }
        }

        return $datas;
    }

    // 设置单元格内容 根据列（数字）和行
    public function setCellValueByColumnAndRow($column, $row, $value)
    {
        $this->sheet->setCellValueByColumnAndRow($column, $row, $value);
    }

    // 设置单元格内容 根据列（字符串）和行 支持定义类型
    public function setCellValueByColumnAndRowAndType(
        $column,
        $row,
        $value,
        $type = DataType::TYPE_STRING
    ) {
        $this->sheet->getCell(Coordinate::stringFromColumnIndex($column) . $row)->setValueExplicit(
            $value,
            $type
        );
    }

    public function save($fileName = null)
    {
        $fileName = $fileName ?? $this->sheet->getTitle();
        $writer = IOFactory::createWriter($this->sheet->getParent(), 'Xlsx');
        $fileName .= $fileName;
        $writer->save($fileName);

        return $fileName;
    }

}