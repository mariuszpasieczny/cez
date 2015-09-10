<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/../library/PHPExcel/PHPExcel.php';

class Utils_File_Reader_PHPExcel implements Utils_File_Reader_Interface
{
    
    protected $_objPHPExcel;
    
    public function __construct($config) {
        $this->_objReader = PHPExcel_IOFactory::createReader($config['readerType']);
        $this->_objReader->setReadDataOnly($config['readOnly']);
        if ($config['charset']) {
            $this->_objReader->setInputEncoding($config['charset']);
        }
    }
    
    public function read($file, $sheet = 1) {
        $objPHPExcel = $this->_objReader->load($file);
        $objPHPExcel->setActiveSheetIndex(--$sheet);
        return $objPHPExcel->getActiveSheet();
    }
    
}