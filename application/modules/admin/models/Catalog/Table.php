<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Catalog_Table extends Application_Db_Table
{
    protected $_name = 'catalog';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Catalog_Row';
    protected $_rowsetClass = 'Application_Db_Table_Rowset';
    protected $_lazyLoading = false;
    protected $_cacheInClass = true;
    
    protected $_dependentTables = array('Application_Model_Returns_Table');
    
    protected $_referenceMap = array(
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
        return parent::select()->setIntegrityCheck(false)->from($this->_from ? $this->_from : 'catalogview');
    }
    
}