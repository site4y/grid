<?php

namespace pdima88\pdgrid\Filter;

use pdima88\pdgrid\Filter;
use pdima88\php\Assets;
use Nette\Utils\Html;

class Filter_Text extends Filter
{
    protected $dataList = null;
    protected $dataListId = null;

    static $dataListIds = [];

    public function init(&$column)
    {
        if ($this->hasRequestParam()) {
            $this->_value = $this->getRequestParam();
        }

        Assets::getInstance()->addScript('
            function S4Y_grid_filter_text_help() {
                eModal.alert("Для поиска по шаблону используйте в тексте:<br>" +
                    "<b>знаки процента %</b> - любое количество (в.т.ч. и 0), любых символов<br>" +
                    "<b>подчеркивания _</b> - один любой символ. <br>" +
                    "Например, чтобы найти элементы начинающиеся с \"ав\" - используйте шаблон ав%, " +
                    "чтобы найти оканчивающиеся на \"ая\" - шаблон %ая.<br><br> " +
                    "По умолчанию (если знак % не используется в шаблоне) будут отобраны все элементы, " +
                    "содержащие заданный шаблон в любом месте строки.<br><br> " +
                    "Если вам нужно найти сам знак % или подчеркивания, экранируйте его с помощью " +
                    "обратной косой черты - \\\\%, \\\\_<br><br>" +
                    "Чтобы найти все пустые поля используйте шаблон _<br>чтобы найти все непустые поля - %",
                    "Фильтр по тексту: подсказка");
            } 
        ', self::class .':js');

        if (isset($column['filterList'])) {
            $this->dataList = [];
            foreach ($column['filterList'] as $item) {
                if (is_array($item)) {
                    if (isset($item['title'])) $this->dataList[] = $item['title'];
                    elseif (isset($item['name'])) $this->dataList[] = $item['name'];
                } else {
                    $this->dataList[] = $item;
                }
            }

            $this->dataListId = 'filter_'.$this->_name.'_datalist'; $i = 1;
            while (isset(self::$dataListIds[$this->dataListId])) {
                $this->dataListId = 'filter_'.$this->_name.'_datalist'.(++$i);
            }
            self::$dataListIds[$this->dataListId] = true;
        }
    }

    function renderFilter()
    {
        $input = Html::el('input')
            ->type('text')
            ->name('filter_'.$this->_name)
            ->class("form-control input-sm")
            ->data('filter', $this->_name);
        if ($this->isActive()) $input->value($this->_value);

        $div = Html::el('div');

        $div->addHtml($input);

        if ($this->dataList) {
            $input->list($this->dataListId);
            /** @var Html $dl */
            $dl = Html::el('datalist')
                ->id($this->dataListId);
            foreach ($this->dataList as $s) {
                $dl->addHtml(
                    Html::el('option')->value($s)
                );
            }
            $div->addHtml($dl);
        }

        $div->addHtml(
            Html::el('span')->class('form-control-clear glyphicon glyphicon-remove'.
                ($this->isActive() ? '' : ' hidden')
            )->filterId($this->_name)
        );

        $div->addHtml(
            Html::el('a')->class('form-control-feedback glyphicon glyphicon-question-sign')
                ->onclick('S4Y_grid_filter_text_help()')
        );

        $div->class('has-clear has-feedback feedback-hidden');

        return $div;
    }

    function isActive()
    {
        return isset($this->_value) && $this->_value !== '';
    }

    function getWhere()
    {
        if ($this->isActive()) {
            $v = $this->_value;
            if ($v == '_') return $this->_grid->$d->quoteIdentifier($this->_name) . ' IS NULL OR '
                                 .$db->quoteIdentifier($this->_name).' = \'\'';
            $vc = str_replace('\\%', '', $v);
            if (strpos($vc, '%') === false) $v = '%'.$v.'%';
            return $db->quoteIdentifier($this->_name) . ' LIKE ' .
            $db->quote($v);
        }
        return '';
    }
}