<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Orders_Table extends Application_Db_Table
{
    protected $_name = 'orders';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Orders_Row';
    
    protected $_referenceMap = array(
        'Status' => array(
            'columns' => 'statusid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'User' => array(
            'columns' => 'userid',
            'refTableClass' => 'Application_Model_Users_Table',
            'refColumns' => 'id'
        )
    );
    
}