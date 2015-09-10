<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Utils_File_Reader_CSV implements Utils_File_Reader_Interface
{
    
    protected $_SplFileObject;
    
    public function __construct($config = null) {
        
    }
    
    public function read($file) {
        $this->_SplFileObject = new SplFileObject($file);
        $this->_SplFileObject->setFlags(SplFileObject::READ_CSV);
        return $this;
    }
    
    public function getRowIterator($startRow = 1, $endRow = null)
    {
        return new Utils_File_Reader_CSV_RowIterator($this->_SplFileObject, $startRow, $endRow);
    }
    
}