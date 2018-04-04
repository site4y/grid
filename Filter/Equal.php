<?php

namespace pdima88\pdgrid\Filter;

use pdima88\pdgrid\Filter\Base as BaseFilter;
use Nette\Utils\Html;

class Equal extends BaseFilter
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
            ->class("form-control input-sm")
            ->data('filter', $this->_name);
        if ($this->isActive()) $input->value($this->_value);

        return Html::el('div',
            $input .
            Html::el('span')->class('form-control-clear glyphicon glyphicon-remove'.
                ($this->isActive() ? '' : ' hidden'))->filterId($this->_name)
        )->_class('has-clear');

    }

    function isActive()
    {
        return isset($this->_value) && $this->_value !== '';
    }

    function getWhere()
    {
        if ($this->isActive()) {
            $v = $this->_value;
            return Grid::$db->quoteIdentifier($this->_name) . ' = ' .Grid::$db->quote($v);
        }
        return '';
    }
}