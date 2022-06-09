<?php

namespace Onekb\ImportBot\Processing\Adapter;

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
    protected array $config;

    public function __construct($config, $errorTemplate)
    {
        $this->config = $config;
        $this->excel = new Xlsx($errorTemplate);
        $this->sheet = $this->excel->getActiveSheet();
    }

    public function output(array $data, $fileName = null)
    {
        $title = $this->sheet->getTitle();
        $dataStartLine = $this->config['dataStartLine'];
        $column = count($title) + 1;
        foreach ($data as $key => $value) {
            $errorArr = [];
            array_map(function ($item) use (&$errorArr) {
                $errorArr += $item;
            }, $value);
            $this->sheet->setCellValueByColumnAndRow(
                $column,
                $key + $dataStartLine,
                join(',', $errorArr)
            );
        }

        $this->sheet->save($fileName);
    }


}