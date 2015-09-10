<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Db_Table_PHPExcel_Table extends Zend_Db_Table_Abstract
{
    
    protected $_rowsetClass = 'Application_Db_Table_PHPExcel_Rowset';
    protected $_rowClass = 'Application_Db_Table_PHPExcel_Row';
    
}