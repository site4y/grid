<?php

namespace pdima88\pdgrid\Filter;

use pdima88\pdgrid\Filter;
use Nette\Utils\Html;

class Equal extends Filter
{

    public function init(&$column)
    {
        if ($this->hasRequestParam()) {
            $this->_value = $this->getRequestParam();
        }
    }

    function renderFilter()
    {
        $input = Html::el('input')->type('text')
            ->name('filter_'.$this->_name)
            ->setClass("form-control input-sm");
        $input->data('filter', $this->_name);
        if ($this->isActive()) $input->value($this->_value);

        return Html::el('div')->addHtml(
            $input)->addHtml(
            Html::el('span')->setClass('form-control-clear glyphicon glyphicon-remove hidden'))
        ->setClass('has-clear');

    }

    function isActive()
    {
        return isset($this->_value) && $this->_value !== '';
    }

    function getWhere()
    {
        if ($this->isActive()) {
            $db = \Zend_Db_Table::getDefaultAdapter();
            $v = $this->_value;
            return $db->quoteIdentifier($this->_name) . ' = ' .
            $db->quote($v);
        }
        return '';
    }
}