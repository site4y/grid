<?php

namespace s4y\grid;

/**
 * Используется для разделения получаемых данных с БД постранично
 *
 * @property int countPerPage Количество записей на страницу, если 0 - постраничное разделение не используется
 * @property int count Общее количество записей
 * @property int pageCount Количество страниц
 * @property int page Текущая страница (по умолчанию автоматически подхватывается из REQUEST параметра page)
 * @property int first Номер первой записи (начиная с 1)
 * @property int last Номер последней записи на тек.странице
 */
class Paginator
{
    protected $_db;

    protected $_tpl = null;

    protected $_countPerPage;

    protected $_count = 0;

    protected $_pageCount = 0;

    protected $_page = 0;

    /**
     * Db_Paginator constructor.
     * @param $db Zend_Db_Adapter_Abstract|array Подключение к базе данных или массив данных
     */
    public function __construct($db, $tpl = null, $countPerPage = 20) {
        $this->_db = $db;
        $this->_tpl = $tpl;
        $this->_countPerPage = $countPerPage;
        if (isset($_REQUEST['page']))
            $this->_page = intval($_REQUEST['page']);
    }

    public function queryCount($sql = null, $params = null) {
        if (!is_array($this->_db)) {
            $this->_count = $this->_db->fetchOne($sql, $params);
        } else {
            $this->_count = count($this->_db);
        }
        $this->update();
    }

    public function getPageCount() {
        return ceil($this->_count / $this->_countPerPage);
    }

    public function getPage() {
        $p = $this->_page;
        if ($p <= 0) {
            $p = 1;
        }
        if ($p > $this->_pageCount) {
            $p = $this->_pageCount;
        }
        return $p;
    }

    public function setCountPerPage($countPerPage) {
        $this->_countPerPage = $countPerPage;
        $this->update();
    }

    public function setPage($page) {
        $this->_page = (int) $page;
        $this->update();
    }

    public function getFirstNumber() {
        if ($this->page) {
            return ($this->page - 1) * $this->countPerPage + 1;
        } else return 0;
    }

    public function getLastNumber() {
        if ($this->page) {
            if ($this->countPerPage > 0) {
                return min($this->count, ($this->page) * $this->countPerPage);
            } else {
                return ($this->count);
            }
        } else return 0;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'count': return $this->_count;
            case 'countPerPage': return $this->_countPerPage;
            case 'page': return $this->getPage();
            case 'pageCount': return $this->getPageCount();
            case 'first': return $this->getFirstNumber();
            case 'last': return $this->getLastNumber();
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'countPerPage':
                $this->setCountPerPage($value);
                break;
            case 'page':
                $this->setPage($value);
                break;
        }
    }

    public function update()
    {
        if ($this->_countPerPage > 0) {
            $this->_pageCount = ceil($this->_count / $this->_countPerPage);
        } else {
            $this->_pageCount = 1;
        }
        if (isset($this->_tpl)) {
            $this->_tpl->setVar('CURRENT_PAGE', $this->page);
            $this->_tpl->setVar('PAGE_COUNT', $this->pageCount);
            $this->_tpl->setVar('COUNTPERPAGE', $this->countPerPage);
        }
    }

    protected function sqlAddPageParams($sql)
    {
        $p = $this->getPage();
        if ($this->_countPerPage > 0 && $p > 0) {
            return $sql . ' LIMIT ' . (($p-1) * $this->_countPerPage) . ',' . $this->_countPerPage;
        }
        return $sql;
    }

    public function fetchAll($sql, $bind = array(), $fetchMode = null)
    {
        if (is_array($this->_db)) {
            if ($this->_countPerPage == 0) return $this->_db;
            $p = $this->getPage();
            $start = (($p-1) * $this->_countPerPage);
            $end = $start + $this->_countPerPage;
            if ($start == 0 && $end == $this->_count) return $this->_db;
            $i = 0; $arr = [];
            foreach ($this->_db as $item) {
                if ($i >= $start && $i < $end) {
                    $arr[] = $item;
                }
                $i++;
            }
            return $arr;
        } else {
            $sql = $this->sqlAddPageParams($sql);
            return $this->_db->fetchAll($sql, $bind, $fetchMode);
        }
    }

    public function prepare($sql)
    {
        if (is_array($this->_db)) return null;
        $sql = $this->sqlAddPageParams($sql);
        $stmt = $this->_db->prepare($sql);
        return $stmt;
    }


}