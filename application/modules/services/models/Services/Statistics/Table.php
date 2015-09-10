<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_Statistics_Table extends Application_Db_Table
{
    protected $_name = 'servicesstatistics';// table name
    protected $_primary = 'technicianid'; // primary column name
    protected $_rowsetClass = 'Application_Db_Table_Rowset';
    
}