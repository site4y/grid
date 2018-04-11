<?php

namespace pdima88\pdgrid\Export;

use pdima88\pdgrid\Export;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

// TODO: работает, но надо доделать форматирование

class Excel extends Export {

    function export($filename, $title, $data)
    {
        $xl = new Spreadsheet();
        
        $xl->setActiveSheetIndex(0);
        $sheet = $xl->getActiveSheet();

        $sheet->setTitle(substr($title,0,31));

        $c = 1;
        if ($this->_grid->options['rownum']) $c++;
        foreach ($this->_grid->columns as $colId => &$col) {
            $sheet->setCellValueByColumnAndRow($c, 2, isset($col['title']) ? $col['title'] : '');
            $c++;
        }
        unset($col);

        foreach ($data as $i => $row) {
            $c = 1;
            if ($this->_grid->options['rownum']) {
                $sheet->setCellValueByColumnAndRow($c, $i+3, $i+1);
                $c++;
            }
            foreach ($this->_grid->columns as $colId => &$col) {
                $sheet->setCellValueByColumnAndRow($c, $i+3, isset($row[$colId]) ? $row[$colId] : '');
                $c++;
            }
            unset($col);
        }

        $colCount = 2;

        //$sheet->mergeCells('R1C1:R1C'.$colCount);
        $sheet->setCellValue('A1', $title);
        /*$sheet->getStyle('A1')->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        );*/

        header ( "Expires: " . gmdate("D,d M YH:i:s") . " GMT" );
        header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
        header ( "Cache-Control: no-cache, must-revalidate" );
        header ( "Pragma: no-cache" );

        header('Content-Type: text/csv');
        //header ( "Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" );
        header ( "Content-Disposition: attachment; filename=$filename.csv" );

        //$xlWriter = PHPExcel_IOFactory::createWriter($xl, 'Excel2007');
        //$xlWriter->save('php://output');

        ob_start();
        $xlWriter = new Csv($xl);
        //$xlWriter->setUseBOM(true);
        //$xlWriter->setExcelCompatibility(true);
        $xlWriter->setDelimiter(';');
        $xlWriter->save('php://output');
        $s = ob_get_contents();
        ob_end_clean();
        $r = iconv('UTF-8', 'Windows-1251//IGNORE', $s);
        if ($r != FALSE) {
            echo $r;
        } else {
            echo $s;
        }
    }
}