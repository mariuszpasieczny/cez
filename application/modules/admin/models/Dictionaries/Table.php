<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Dictionaries_Table extends Application_Db_Table
{
    protected $_name = 'dictionaries';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Dictionaries_Row';
    protected $_rowsetClass = 'Application_Db_Table_Rowset';
    protected $_lazyLoading = false;
    protected $_cacheInClass = true;
    
    protected $_dependentTables = array('Application_Model_Products_Table', 'Application_Model_Dictionaries_Attributes_Table');
    
    protected $_referenceMap = array(
        'Children' => array(
            'columns' => 'parentid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Status' => array(
            'columns' => 'statusid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        )
    );
    
    public function init($config = array()) {
        $this->setCacheInClass(true);
        $this->_setCache(Application_Db_Table::getDefaultCache());
    }
    
    public function select() {
        if ($this->_lazyLoading === true) {
            return parent::select();
        }
        return parent::select()->setIntegrityCheck(false)->from($this->_from ? $this->_from : 'dictionariesview');
    }
    
    public function getDictionaryList($module = null, $acronym = null) {
        $rowset = $this->getAll(array('parentid' => 1));
        if (!$rowset->count()) {
            return $rowset;
        }
        $rowset = $rowset->find('dictionary','acronym')->getChildren();
        if (!$module) {
            return $rowset;
        }
        $rowset = $rowset->find($module, 'acronym')->getChildren();
        if (!$acronym) {
            return $rowset;
        }
        
        return $rowset->find($acronym, 'acronym')->getChildren();
    }
    
    public function getTypeList($module = null) {
        $rowset = $this->getAll(array('parentid' => 1))->find('type','acronym')->getChildren();
        if (!$module) {
            return $rowset;
        }
        $row = $rowset->find($module, 'acronym');
        if (!$row) {
            throw new Exception('Typ dla modułu ' . $module . ' nie znaleziony');
        }
        return $row->getChildren();
    }
    
    public function getStatusList($module = null) {
        $rowset = $this->getAll(array('parentid' => 1))->find('status','acronym')->getChildren();
        if (!$module) {
            return $rowset;
        }
        $row = $rowset->find($module, 'acronym');
        if (!$row) {
            throw new Exception('Status dla modułu ' . $module . ' nie znaleziony');
        }
        return $row->getChildren();
    }
    
    public function getAttributeList($module = null) {
        $rowset = $this->getAll(array('parentid' => 1))->find('attribute','acronym')->getChildren();
        if (!$module) {
            return $rowset;
        }
        $row = $rowset->find($module, 'acronym');
        if (!$row) {
            throw new Exception('Atrybut dla modułu ' . $module . ' nie znaleziony');
        }
        return $row->getChildren();
    }
    
}