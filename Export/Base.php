<?php

namespace pdima88\pdgrid\Export;

abstract class Base
{
    protected $_grid = null;

    static function create($type, $grid) {
        if (class_exists('S4Y_Grid_Export_'.ucfirst($type))) {
            $className = 'S4Y_Grid_Export_'.ucfirst($type);
        } elseif (class_exists($type)) {
            $className = $type;
        } else {
            throw new Exception('Filter '.$type.' not defined!');
        }
        $export = new $className($grid);
        return $export;
    }

    protected function __construct($grid) {
        $this->_grid = $grid;
    }

    abstract public function export($filename, $title, $data);
}