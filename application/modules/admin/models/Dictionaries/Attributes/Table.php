<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Dictionaries_Attributes_Table extends Application_Db_Table
{
    
    protected $_name = 'dictionaryattributes';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Dictionaries_Attributes_Solution';
    protected $_rowsetClass = 'Application_Db_Table_Rowset';
    
    protected $_referenceMap = array(
        'Solution' => array(
            'columns' => 'value',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Error' => array(
            'columns' => 'value',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Dictionary' => array(
            'columns' => 'entryid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        )
    );
    
}