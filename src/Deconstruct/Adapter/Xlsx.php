<?php

namespace Onekb\ImportBot\Deconstruct\Adapter;

use League\Flysystem\Filesystem;
use Onekb\ImportBot\Config;
use Onekb\ImportBot\Deconstruct\Interfaces\FileInterface;
use Onekb\ImportBot\Deconstruct\Interfaces\SheetInterface;
use Onekb\ImportBot\Di;
use PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;

class Xlsx implements FileInterface
{
    protected $file;
    protected Spreadsheet $excel;
    protected Config $config;

    public function __construct($file, Config $config)
    {
        $this->file = $file;
        $this->config = $config;
        $this->loadFile();
    }

    // 载入文件
    protected function loadFile()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');

        $file = Di::$container['filesystem'];
        $buffer = $file->readStream($this->file);
        file_put_contents($tempFile, $buffer);
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $this->excel = $reader->load($tempFile);
        unlink($tempFile);
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getFileName()
    {
        return basename($this->file);
    }

    public function getSheetNames(): array
    {
        return $this->excel->getSheetNames();
    }

    public function getSheet($sheetName): SheetInterface
    {
        $sheet = $this->excel->getSheetByName($sheetName);
        if (! $sheet) {
            throw new \Exception('Sheet not found');
        }

        return new XlsxSheet($sheet, $this->config);
    }

    public function getActiveSheet(): SheetInterface
    {
        $sheet = $this->excel->getActiveSheet();

        return new XlsxSheet($sheet, $this->config);
    }

    public function save($fileName = null)
    {
        $fileName = $fileName ?? $this->file;
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer = IOFactory::createWriter($this->excel, 'Xlsx');
        $writer->save($tempFile);

        $file = Di::$container['filesystem'];
        $buffer = $file->readStream($tempFile);
        file_put_contents($this->file, $buffer);
        unlink($tempFile);
    }
}