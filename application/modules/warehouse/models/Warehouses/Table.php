<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Warehouses_Table extends Application_Db_Table
{
    protected $_name = 'warehouses';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Warehouses_Row';
    
    protected $_dependentTables = array('Application_Model_Products_Table', 'Application_Model_Orders_Table');
    
    protected $_referenceMap = array(
        'Parent' => array(
            'columns' => 'parentid',
            'refTableClass' => 'Application_Model_Warehouses_Table',
            'refColumns' => 'id'
        ),
        'Type' => array(
            'columns' => 'typeid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Area' => array(
            'columns' => 'areaid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        )
    );
    
    public function getSearchFields() {
        $fields = parent::getSearchFields();
        $fields[] = 'type';
        $fields[] = 'area';
        return $fields;
    }
    
}