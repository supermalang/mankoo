<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv as ReaderCsv;
use PhpOffice\PhpSpreadsheet\Reader\Xls as ReaderXls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\File\Exception\ExtensionFileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use WorksheetReadFilter;

class SpreadsheetHelper
{
    private $targetDirectory;
    private $worksheetName;
    private $worksheetColumns;
    private $worksheetRows;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * Reads a file and returns Spreadsheet data.
     *
     * @param string              $filename       Full address of the file to read
     * @param bool                $dataOnly       If true, reads only the data
     * @param bool                $firstSheetOnly If true, reads only the first sheet
     * @param WorksheetReadFilter $filterSubset
     *
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public function readFile($filename, $dataOnly = true, $firstSheetOnly = true, WorksheetReadFilter $filterSubset = null)
    {
        // Using the extension can bring some errors. A CSV file can have an xls extension.
        // So we are going to use the file type instead
        // $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $fileType = IOFactory::identify($filename);

        switch ($fileType) {
            case 'Xls':
                $reader = new ReaderXls();

                break;

            case 'Xlsx':
                $reader = new ReaderXlsx();

                break;

            case 'Csv':
                $reader = new ReaderCsv();

                break;

            default:
                throw new ExtensionFileException('Invalid file extension');
        }

        if ($dataOnly) {
            // Advise the Reader that we only want to load cell data
            $reader->setReadDataOnly(true);
        }

        if ($firstSheetOnly) {
            // To not go into complex tasks we are going to focus only on the first worksheet of the file by default
            $worksheetData = $reader->listWorksheetInfo($filename);
            $worksheetData = $worksheetData[0];

            $this->worksheetName = $worksheetData['worksheetName'];
            $this->worksheetColumns = $worksheetData['totalColumns'];
            $this->worksheetRows = $worksheetData['totalRows'];

            $reader->setLoadSheetsOnly($worksheetData['worksheetName']);
        }

        if (isset($filterSubset)) {
            $reader->setReadFilter($filterSubset);
        }

        return $reader->load($filename);
    }

    /**
     * Returns the number of rows in the given worksheet.
     *
     * @param string $filename
     * @param string $worksheetName
     *
     * @return int Number of rows
     */
    public function getWorksheetRows($filename, $worksheetName = null)
    {
        $spreadsheet = $this->readFile($filename);

        if (isset($worksheetName)) {
            $worksheet = $spreadsheet->getSheetByName($worksheetName);
        } else {
            $worksheet = $spreadsheet->getActiveSheet();
        }

        return $worksheet->getHighestRow();
    }

    /**
     * Extract a Worksheet from a Spreadsheet.
     *
     * @param Spreadsheet $spreadsheet   The spreadsheet file from which to extract the worksheet
     * @param string      $worksheetName The name of the worksheet to extract. If not defined, the first worksheet is extracted
     *
     * @return \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public function getWorksheet(Spreadsheet $spreadsheet, $worksheetName = null)
    {
        $worksheetName = $worksheetName ?? $this->worksheetName;
        $worksheet = $spreadsheet->getSheetByName($worksheetName);

        if (!isset($worksheet)) {
            throw new \Exception('Worksheet not found');
        }

        return $worksheet;
    }

    /**
     * Returns data from a Worksheet.
     *
     * @param Worksheet $worksheet The worksheet from which to extract the data
     *
     * @return array
     */
    public function createDataFromWorksheet(Worksheet $worksheet)
    {
        $data = [];
        $data = [
            'columnNames' => [],
            'columnValues' => [],
        ];

        foreach ($worksheet->getRowIterator() as $row) {
            $rowIndex = $row->getRowIndex(); // starts at 1

            if ($rowIndex > 1) {
                $data['columnValues'][$rowIndex] = [];
            }
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop over all cells, even if it is not set
            foreach ($cellIterator as $cell) {
                if (1 === $rowIndex) {
                    $data['columnNames'][] = $cell->getCalculatedValue();
                }
                if ($rowIndex > 1) {
                    $data['columnValues'][$rowIndex][] = $cell->getCalculatedValue();
                }
            }
        }

        return $data;
    }

    public function createDataFromSpreadsheet(Spreadsheet $spreadsheet)
    {
        $data = [];
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $worksheetTitle = $worksheet->getTitle();
            $data[$worksheetTitle] = [
                'columnNames' => [],
                'columnValues' => [],
            ];
            foreach ($worksheet->getRowIterator() as $row) {
                $rowIndex = $row->getRowIndex();
                if ($rowIndex > 1) {
                    $data[$worksheetTitle]['columnValues'][$rowIndex] = [];
                }
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Loop over all cells, even if it is not set
                foreach ($cellIterator as $cell) {
                    if (1 === $rowIndex) {
                        $data[$worksheetTitle]['columnNames'][] = $cell->getCalculatedValue();
                    }
                    if ($rowIndex > 1) {
                        $data[$worksheetTitle]['columnValues'][$rowIndex][] = $cell->getCalculatedValue();
                    }
                }
            }
        }

        return $data;
    }

    protected function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
