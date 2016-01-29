<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_Codes_Table extends Application_Db_Table
{
    protected $_name = 'servicecodes';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Services_Codes_Row';
    protected $_rowsetClass = 'Application_Db_Table_Rowset';
    
    protected $_referenceMap = array(
        'Service' => array(
            'columns' => 'serviceid',
            'refTableClass' => 'Application_Model_Services_Table',
            'refColumns' => 'id'
        )
    );
    
    public function select() {
        if ($this->_lazyLoading === true) {
            return parent::select();
        }
        $select = parent::select()->setIntegrityCheck(false)->from(($this->_schema ? ($this->_schema . '.') : '') . 'servicecodesview');
        return $select;
    }
    
}