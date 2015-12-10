<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Products_Migration extends Application_Model_Products_Row {

    const COLUMN_SERIAL = 'E';
    const COLUMN_RELEASEDATE = 'C';
    const COLUMN_NAME = 'F';
    const COLUMN_QUANTITY = 'G';
    const COLUMN_TECHNICIAN = 'A';
    const COLUMN_PAIREDCARD = 'H';
    
    protected $_technician;
    protected $_releasedate;
    
    public function setFromCellIterator($data) {
        foreach ($data as $key => $cell) {
            switch ($key) {
                case self::COLUMN_SERIAL:
                    $this->serial = $cell->getCalculatedValue();
                    break;
                case self::COLUMN_NAME:
                    $this->name = $cell->getValue();
                    break;
                case self::COLUMN_PAIREDCARD:
                    $this->pairedcard = $cell->getValue();
                    break;
                case self::COLUMN_QUANTITY:
                    $this->quantity = $cell->getValue();
                    break;
                case self::COLUMN_RELEASEDATE:
                    $value = $cell->getValue();
                    if (!empty($value)) {
                        if (!strtotime($value)) {
                            //$planneddate = new Zend_Date(PHPExcel_Shared_Date::ExcelToPHP($cell));
                            $value = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()));
                        } else {
                            $value = $cell->getValue();
                        }
                        $releasedate = new Zend_Date($value);
                        $this->_releasedate = $releasedate->get('yyyy-MM-dd');
                        //var_dump($this->planneddate);
                    }
                    break;
                case self::COLUMN_TECHNICIAN:
                    $technician = $cell->getCalculatedValue();
                    if (preg_match('/^(\w+)\s*(\w+)$/', $technician, $found)) {
                        //list($lastName, $firstName) = @explode('\s', trim($technician));
                        $this->_technician = array(
                            'firstname' => trim($found[2]), 
                            'lastname' => trim($found[1]), 
                            //'email' => $technician,
                            //'symbol' => $technician
                        );
                    }
                    break;
            }
        }
        return $this;
    }

}
