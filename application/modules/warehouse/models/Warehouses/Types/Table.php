<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Warehouses_Types_Table extends Application_Db_Table
{
    protected $_name = 'warehousetypes';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Warehouses_Types_Row';
    
}