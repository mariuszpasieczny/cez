<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Dictionaries_Attributes_Solution extends Application_Db_Table_Row
{
    
    const COLUMN_ERRORCODE = 'J';
    const COLUMN_SOLUTIONCODE = 'K';
    
    protected $_errorcode;
    protected $_solutioncode;
    
    public function setFromCellIterator($data) {
        foreach ($data as $key => $cell) {
            switch ($key) {
                case self::COLUMN_ERRORCODE:
                    $this->_errorcode = $cell->getValue();
                    break;
                case self::COLUMN_SOLUTIONCODE:
                    $this->_solutioncode = $cell->getValue();
                    break;
            }
        }
        return $this;
    }
    
    public function getValue() {
        if (!$this->id) {
            $table = new Application_Model_Dictionaries_Attributes_Table();
            return new Application_Model_Dictionaries_Attributes_Solution(array(
                'table' => $table,
                'data' => $this->toArray(),
                'readOnly' => false,
                'stored' => true
            ));
        }
        return parent::findParentRow('Application_Model_Dictionaries_Table', 'Error');
    }
    
}