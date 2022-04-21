<?php

// https://phpspreadsheet.readthedocs.io/en/latest/topics/reading-files/#reading-only-specific-columns-and-rows-from-a-file-read-filters
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class WorksheetReadFilter implements IReadFilter
{
    private $startRow = 0;
    private $endRow = 0;
    private $columns = [];

    /**
     * Get the list of rows and columns to read.
     *
     * @param int   $startRow The first row number
     * @param int   $endRow   The last row number
     * @param array $columns  The list of columns
     */
    public function __construct($startRow, $endRow, $columns)
    {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
        $this->columns = $columns;
    }

    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        //  Only read the rows and columns that were configured
        if ($row >= $this->startRow && $row <= $this->endRow) {
            if (in_array($columnAddress, $this->columns)) {
                return true;
            }
        }

        return false;
    }
}
