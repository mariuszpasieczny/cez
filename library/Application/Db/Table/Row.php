<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Db_Table_Row extends Zend_Db_Table_Row
{
    
    public function __call($method, array $args) {
        if (strpos($method, 'get') === 0) {
            $columnName = '_' . strtolower(substr($method, 3, strlen($method)));
            return $this->{$columnName};
        }
        if (strpos($method, 'set') === 0) {
            $columnName = '_' . strtolower(substr($method, 3, strlen($method)));
            return $this->{$columnName}($arguments);
        }
        return parent::__call($method, $arguments);
    }

    public function findParentRow($parentTable, $ruleKey = null, Zend_Db_Table_Select $select = null) {
        return parent::findParentRow($parentTable, $ruleKey, $select);
    }

    public function findDependentRowset($dependentTable, $ruleKey = null, Zend_Db_Table_Select $select = null) {
        return parent::findDependentRowset($dependentTable, $ruleKey, $select);
    }
    
    public function _update() {
        parent::_update();
        foreach ($this->_modifiedFields as $key => $modified) {
            $this->getTable()->getAdapter()->insert('history', 
                array('userid' => Zend_Auth::getInstance()->getIdentity()->id,
                    'tablename' => $this->getTable()->getName(),
                    'rowid' => $this->id,
                    'columnname' => $key,
                    'value' => $this->{$key}));
        }
    }
    
    public function __toString() {
        return $this->id;
    }
    
    public function isModified() {
        return !empty($this->_modifiedFields);
    }
    
}