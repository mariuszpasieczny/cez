<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Db_Table extends Zend_Db_Table_Abstract {

    const ITEMS_PER_PAGE = 50;
    const ITEMS_ORDER_BY = 'id DESC';
    const CACHE_IN_CLASS = 'cacheInClass';

    protected $_pageNumber;
    protected $_itemCountPerPage;
    protected $_paginator;
    protected $_lazyLoading = true;
    protected $_orderBy = Application_Db_Table::ITEMS_ORDER_BY;
    protected $_where;
    protected $_from;
    protected static $_defaultCache = null;
    protected $_cacheInClass = false;
    protected $_cache = false;

    public function getName() {
        return $this->_name;
    }

    public static function setDefaultCache($cache = null) {
        self::$_defaultCache = self::_setupCache($cache);
    }

    public static function getDefaultCache($cache = null) {
        return self::$_defaultCache;
    }

    protected static function _setupCache($cache) {
        if ($cache === null) {
            return null;
        }
        if (is_string($cache)) {
            require_once 'Zend/Registry.php';
            $cache = Zend_Registry::get($cache);
        }
        if (!$cache instanceof Zend_Cache_Core) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception('Argument must be of type Zend_Cache_Core, or a Registry key where a Zend_Cache_Core object is stored');
        }
        return $cache;
    }

    protected function _setCache($cache) {
        $this->_cache = self::_setupCache($cache);
        return $this;
    }

    public function setCacheInClass($flag) {
        $this->_cacheInClass = (bool) $flag;
        return $this;
    }

    public function cacheInClass() {
        return $this->_cacheInClass;
    }

    public function getCache() {
        return $this->_cache;
    }

    public function clearCache() {
        if (!$this->getCache()) {
            return;
        }
        $this->_cache->clean(
                Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($this->_name)
        );
    }
    
    public function update(array $data, $where) {
        $this->clearCache();
        return parent::update($data, $where);
    }
    
    public function insert(array $data) {
        $this->clearCache();
        return parent::insert($data);
    }
    
    public function delete($where) {
        $this->clearCache();
        return parent::delete($where);
    }

    public function setFrom($from) {
        $this->_from = $from;
    }

    public function clearWhere() {
        $this->_where = array();
    }

    public function setWhere($where) {
        $this->_where[] = $where;
    }

    public function setLazyLoading($flag = true) {
        $this->_lazyLoading = $flag;
    }

    public function getSearchFields() {
        $info = $this->info();
        return $info['cols'];
    }

    public function get() {
        $args = func_get_args();
        $cacheName = $this->_name . '_' . md5(serialize($args));
        $this->setLazyLoading(true);
        if (!$this->cacheInClass() || ($rows = $this->getCache()->load($cacheName)) === false) {
            if (is_array($args[0])) {
                $rowset = parent::find($args[0]);
            } else {
                $rowset = parent::find($args)->current();
            }
            if ($this->cacheInClass()) {
                $this->getCache()->save($rowset, $cacheName, array($this->_name));
            }
        }
        return $rowset;
    }

    public function getAll($params = array(), $rows = null, $root = null) {
        $cacheName = $this->_name . '_' . md5(serialize($params));
        $select = $this->select();
        if ($this->_where) {
            $select->where(join(" AND ", $this->_where));
        }
        if (!isset($rows)) {
            $searchFields = $this->getSearchFields();
            foreach ($params as $key => $value) {
                if ($key == 'search') {
                    $subquery = array();
                    foreach ($searchFields as $column) {
                        $subquery[] = $this->getAdapter()->quoteInto("UPPER($column) LIKE UPPER(?)", '%' . $value . '%');
                    }
                    $select->where(join(" OR ", $subquery));
                }
                if (!in_array($key, $searchFields)) {
                    continue;
                }
                if (!isset($value) || (!is_array($value) && !strlen($value))) {
                    continue;
                }
                if (is_array($value)) {
                    $select->where("$key IN (?)", $value);
                } else if (strpos($value, 'LIKE') !== false) {
                    $select->where("$key LIKE ?", trim(str_replace('LIKE', '', $value)) . '%');
                } else if (strpos($value, '>=') !== false) {
                    $select->where("$key <= ?", trim(str_replace('>=', '', $value)));
                } else if (strpos($value, '<=') !== false) {
                    $select->where("$key >= ?", trim(str_replace('<=', '', $value)));
                } else {
                    $select->where("UPPER($key) = UPPER(?)", $value);
                }
            }

            if ($this->getOrderBy())
                $select->order($this->getOrderBy());
            //echo $select;
            //var_dump($params,$this->getOrderBy());echo $select;
            //if ($params['parentid']==53)try{throw new Exception('test');}catch(Exception$e){var_dump($params,$this->getOrderBy(),$e->getTraceAsString());}
            if (!$this->cacheInClass() || ($rows = $this->getCache()->load($cacheName)) === false) {

                if ($this->getItemCountPerPage() != null) {
                    $this->_setPaginator($select);
                    $rows = $this->getPaginator()->getCurrentItems()->toArray();
                } else {
                    $rows = $select->query()->fetchAll();
                }
                if ($this->cacheInClass()) {
                    $this->getCache()->save($rows, $cacheName, array($this->_name));
                }
            }
        } else {
            if ($root) {
                array_unshift($rows, $this->get($root)->toArray());
            }
            //$rows = $this->_setPaginator($rows)->getCurrentItems();
        }

        $data = array(
            'table' => $this,
            'data' => $rows,
            'readOnly' => $select->isReadOnly(),
            'rowClass' => $this->_rowClass,
            'stored' => true
        );

        if (!class_exists($this->_rowsetClass)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($this->_rowsetClass);
        }
        return new $this->_rowsetClass($data);
    }

    public function getTree($parent = 0, $level = 0) {
        $this->setItemCountPerPage(null);
        $rows = array(); //var_dump('------------','parent:'.$parent,'level:'.$level);
        foreach ($this->getAll(array('parentid' => $parent))->toArray() as $index => $row) {
            $rows[$index] = $row;
            if ($children = $this->getTree($row['id'], ++$level)) {
                //$rows = array_merge($rows, $children);
                $rows[$index]['children'] = $children;
            }
        }//var_dump($rows);
        return $rows;
    }

    /**
     * @param int $no
     * @return $this
     */
    public function setPageNumber($no) {
        $this->_pageNumber = $no;
        return $this;
    }

    public function getPageNumber() {
        return $this->_pageNumber;
    }

    public function setOrderBy($column) {
        $this->_orderBy = $column;
        return $this;
    }

    public function getOrderBy() {
        return $this->_orderBy;
    }

    /**
     * @param int $no
     * @return $this
     */
    public function setItemCountPerPage($no) {
        $this->_itemCountPerPage = $no;
        return $this;
    }

    public function getItemCountPerPage() {
        return $this->_itemCountPerPage;
    }

    /**
     * @param Zend_Db_Table_Select $select
     * @return Paginator
     */
    protected function _setPaginator($select) {
        //PaginationControl::setDefaultViewPartial('NavControls.phtml');
        $this->_paginator = Zend_Paginator::factory($select); //var_dump('page no:'.$this->getPageNumber(),'item count:'.$this->getItemCountPerPage());
        $this->_paginator->setCurrentPageNumber($this->getPageNumber());
        $this->_paginator->setItemCountPerPage($this->getItemCountPerPage());
        return $this->_paginator;
    }

    public function getPaginator() {
        //var_dump(get_class($this),$this->_paginator->getPages());//exit;
        return $this->_paginator;
    }

}
