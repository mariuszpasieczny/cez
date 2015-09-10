<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_Migration_Installation extends Application_Model_Services_Service {

    const COLUMN_NUMBER = 'B';
    const COLUMN_PLANNEDDATE = 'C';
    const COLUMN_TIME = 'D';
    const COLUMN_CLIENT_NUMBER = 'F';
    const COLUMN_SERVICETYPE = 'G';
    const COLUMN_CLIENT_CITY = 'H';
    const COLUMN_CLIENT_STREET = 'I';
    const COLUMN_CLIENT_STREETNO = 'J';
    const COLUMN_CLIENT_APARTMENTNO = 'K';
    const COLUMN_CALENDAR = 'L';
    const COLUMN_TECHNICIAN = 'N';
    const COLUMN_TECHNICALCOMMENTS = 'O';
    const COLUMN_COORDINATORCOMMENTS = 'P';
    const COLUMN_PRODUCTSRETURNED = 'Q';
    const COLUMN_PRODUCTSRELEASED = 'R';
    const COLUMN_INSTALLATIONCODE = 'S';
    const COLUMN_FINISHED = 'T';
    const COLUMN_CLOSED = 'U';
    
    protected $_finished;
    protected $_closed;
    protected $_installationcode;
    
    public function setFromCellIterator($data) {
        foreach ($data as $key => $cell) {
            switch ($key) {
                case self::COLUMN_SERVICETYPE:
                    $this->_servicetype = $cell->getValue();
                    break;
                case self::COLUMN_PLANNEDDATE:
                    //if (!strtotime($cell->getValue())) {
                        //$planneddate = new Zend_Date(PHPExcel_Shared_Date::ExcelToPHP($cell));
                    //    $value = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()));
                    //} else {
                    //    $value = $cell->getValue();
                    //}
                    //$planneddate = new Zend_Date($value);
                    //$this->planneddate = $planneddate->get('yyyy-MM-dd');
                    //var_dump($this->number,$cell->getValue(),$value,$this->planneddate);
                    $value = $cell->getValue();
                    if (!empty($value)) {
                        if (!strtotime($value)) {
                            //$planneddate = new Zend_Date(PHPExcel_Shared_Date::ExcelToPHP($cell));
                            $value = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()));
                        } else {
                            $value = $cell->getValue();
                        }
                        $planneddate = new Zend_Date($value);
                        $this->planneddate = $planneddate->get('yyyy-MM-dd');
                        //var_dump($this->planneddate);
                    }
                    break;
                case self::COLUMN_TIME:
                    list ($this->timefrom, $this->timetill) = explode('-', $cell->getValue());
                    /*//$timefrom = new Zend_Date($from, 'HH:mm');
                    //$this->timefrom = $timefrom->get('HH:mm');
                    //$timetill = new Zend_Date($till, 'HH:mm');
                    //$this->timetill = $timetill->get('HH:mm');
                    $from = str_pad($value, 4, '0', STR_PAD_LEFT);
                    $timefrom = new Zend_Date($from, 'HH:mm');
                    //$this->timefrom = $timefrom->get('HH:mm');//var_dump($cell->getValue(),$this->timefrom);
                    $till = str_pad($till, 4, '0', STR_PAD_LEFT);
                    $timetill = new Zend_Date($till, 'HH:mm');
                    //$this->timetill = $timetill->get('HH:mm');//var_dump($cell->getValue(),$this->timetill);*/
                    break;
                case self::COLUMN_CLIENT_NUMBER:
                    $this->_client['number'] = $cell->getValue();
                    break;
                case self::COLUMN_CLIENT_CITY:
                    $this->_client['city'] = $cell->getValue();
                    break;
                case self::COLUMN_CLIENT_STREET:
                    $this->_client['street'] = $cell->getValue();
                    break;
                case self::COLUMN_CLIENT_STREETNO:
                    $this->_client['streetno'] = $cell->getValue();
                    break;
                case self::COLUMN_CLIENT_APARTMENTNO:
                    $this->_client['apartmentno'] = $cell->getValue();
                    break;
                case self::COLUMN_NUMBER:
                    $this->number = $cell->getValue();
                    break;
                case self::COLUMN_CALENDAR:
                    $this->_calendar = $cell->getValue();
                    break;
                case self::COLUMN_TECHNICALCOMMENTS:
                    $this->technicalcomments = $cell->getValue();
                    break;
                case self::COLUMN_COORDINATORCOMMENTS:
                    $this->coordinatorcomments = $cell->getValue();
                    break;
                case self::COLUMN_INSTALLATIONCODE:
                    $this->_installationcode = $cell->getValue();
                    break;
                case self::COLUMN_PRODUCTSRETURNED:
                    $this->productsreturned = $cell->getValue();
                    break;
                case self::COLUMN_PRODUCTSRELEASED:
                    $this->_productsreleased = $cell->getValue();
                    break;
                case self::COLUMN_FINISHED:
                    $this->_finished = $cell->getValue();
                    break;
                case self::COLUMN_CLOSED:
                    $this->_closed = $cell->getValue();
                    break;
                case self::COLUMN_TECHNICIAN:
                    $technician = $cell->getCalculatedValue();
                    if (!empty($technician)) {
                        list($lastName, $firstName) = @explode(' ', $technician);
                        $this->_technician = array(
                            'firstname' => $firstName, 
                            'lastname' => $lastName, 
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
