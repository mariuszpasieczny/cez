<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Utils_File_Reader_JSON implements Utils_File_Reader_Interface
{
    
    protected $_data;
    
    public function __construct($config) {
        
    }
    
    public function read($file) {
        $content = file_get_contents($file);
        //$this->_reader = new Zend_Config_Json($content);
        $this->_data = Zend_Json::decode(iconv('ISO-8859-2', 'UTF-8', $content));
        return $this;
    }
    
    public function getRowIterator($level)
    {
        return new Utils_File_Reader_JSON_RowIterator($this->_data, $level);
    }
    
}