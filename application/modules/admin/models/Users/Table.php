<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Users_Table extends Application_Db_Table
{
    protected $_name = 'users';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Users_Row';
    protected $_cacheInClass = true;
    
    protected $_referenceMap = array(
        'Role' => array(
            'columns' => 'roleid',
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
        return parent::select()->setIntegrityCheck(false)->from($this->_from ? $this->_from : 'usersview');
    }
    
}