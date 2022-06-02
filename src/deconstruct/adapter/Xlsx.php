<?php

namespace Onekb\ImportBot\Deconstruct\Adapter;

use League\Flysystem\Filesystem;
use Onekb\ImportBot\Deconstruct\Config;
use Onekb\ImportBot\Deconstruct\Interfaces\FileInterface;
use Onekb\ImportBot\Deconstruct\Interfaces\SheetInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;

class Xlsx implements FileInterface
{
    protected $file;
    protected Spreadsheet $excel;

    public function __construct($file)
    {
        $this->file = $file;
        $this->loadFile();
    }

    // 载入文件
    protected function loadFile()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $adapter = new \League\Flysystem\Local\LocalFilesystemAdapter(
            __DIR__ . '/../../../'
        );
        $file = new Filesystem($adapter);
        $buffer = $file->readStream($this->file);
        file_put_contents($tempFile, $buffer);
        $reader = IOFactory::createReader('Xlsx');
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

        return new XlsxSheet($sheet);
    }
}