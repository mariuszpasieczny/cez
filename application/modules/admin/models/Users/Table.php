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
    
    protected $_referenceMap = array(
        'Role' => array(
            'columns' => 'roleid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        )
    );
    
    public function select() {
        if ($this->_lazyLoading === true) {
            return parent::select();
        }
        return parent::select()->setIntegrityCheck(false)->from($this->_from ? $this->_from : array('users' => 'usersview'));
    }
    
    public function getAll($params = array(), $rows = null, $root = null) {
        if (!empty($params['status'])) {
            $this->setWhere($this->getAdapter()->quoteInto("status = ?", $params['status']));
        }
        if (!empty($params['name'])) {
            $where[] = $this->getAdapter()->quoteInto("UPPER(lastname) LIKE UPPER(?)", "{$params['name']}%");
            $where[] = $this->getAdapter()->quoteInto("UPPER(email) LIKE UPPER(?)", "{$params['name']}%");
            $this->setWhere(join(" OR ", $where));
        }
        return parent::getAll($params, $rows, $root);
    }
    
}