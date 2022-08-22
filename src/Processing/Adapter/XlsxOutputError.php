<?php

namespace Onekb\ImportBot\Processing\Adapter;

use Onekb\ImportBot\Config;
use Onekb\ImportBot\Deconstruct\Adapter\Xlsx;
use Onekb\ImportBot\Deconstruct\Interfaces\FileInterface;
use Onekb\ImportBot\Deconstruct\Interfaces\SheetInterface;
use Onekb\ImportBot\Di;
use Onekb\ImportBot\Processing\Interfaces\OutputErrorInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class XlsxOutputError implements OutputErrorInterface
{
    protected FileInterface $excel;
    protected SheetInterface $sheet;
    protected ?Config $config;

    public function __construct(Config $config, $errorTemplate)
    {
        $this->config = $config;
        $this->excel = new Xlsx($errorTemplate, $config);
        $this->sheet = $this->excel->getSheet($this->config->sheet_name);
    }

    public function output(array $data, $fileName = null)
    {
        $title = $this->sheet->getTitle();
        $dataStartLine = $this->config->data_start_line;
        $column = count($title) + 1;
        foreach ($data as $key => $value) {
            $errorArr = [];
            array_map(function ($item) use (&$errorArr) {
                $errorArr += $item;
            }, $value);
            $this->sheet->setCellValueByColumnAndRowAndType(
                $column,
                $key + $dataStartLine,
                join(',', $errorArr)
            );
        }

        return $this->sheet->save($fileName);
    }

    public function outputError(array $data, $fileName = null)
    {
        $dataStartLine = $this->config->data_start_line;
        foreach ($data as $row => $item) {
            foreach ($item as $column => $value) {
                $this->sheet->setCellValueByColumnAndRowAndType(
                    $column + 1,
                    $row + $dataStartLine,
                    $value
                );
            }
        }

        return $this->sheet->save($fileName);
    }

}