<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_XLS_Installation extends Application_Model_Services_Service {

    const COLUMN_NUMBER = 'A';
    const COLUMN_PLANNEDDATE = 'C';
    const COLUMN_TIMEFROM = 'D';
    const COLUMN_TIMETILL = 'E';
    const COLUMN_TECHNICIAN = 'F';
    const COLUMN_CLIENT_NUMBER = 'G';
    const COLUMN_CALENDAR = 'H';
    const COLUMN_SERVICETYPE = 'I';
    const COLUMN_CLIENT_CITY = 'J';
    const COLUMN_CLIENT_STREET = 'K';
    const COLUMN_CLIENT_STREETNO = 'L';
    const COLUMN_CLIENT_APARTMENTNO = 'M';
    const COLUMN_TECHNICIANCODE = 'N';
    const COLUMN_DATEFINISHED = 'O';
    const COLUMN_INSTALLATIONCODE = 'P';
    const COLUMN_INSTALLATIONCANCELCODE = 'Q';
    const COLUMN_PRODUCTSRELEASED = 'R';
    const COLUMN_PRODUCTSRETURNED = 'S';
    const COLUMN_TECHNICALCOMMENTS = 'T';
    const COLUMN_COORDINATORCOMMENTS = 'U';
    const COLUMN_DOCUMENTSPASSED = 'V';
    const COLUMN_CLOSEDUPC = 'W';
    const COLUMN_PERFORMED = 'X';
    const COLUMN_STATUS = 'Y';

    public function toXlsArray() {//var_dump($this->toArray());exit;
        $array = array();
        foreach ($this->_data as $key => $value) {
            switch ($key) {
                default:
                    $column = 'COLUMN_' . strtoupper($key);
                    //$array[self::$column] = $value;
                    $r = new ReflectionClass('Application_Model_Services_XLS_Installation');
                    $id = $r->getConstant($column);
                    if ($id) {
                        $array[$id] = $value;
                    }
                    break;
                case 'clientnumber':
                    $array[self::COLUMN_CLIENT_NUMBER] = $value;
                    break;
                case 'clientcity':
                    $array[self::COLUMN_CLIENT_CITY] = $value;
                    break;
                case 'clientstreet':
                    $array[self::COLUMN_CLIENT_STREET] = $value;
                    break;
                case 'clientstreetno':
                    $array[self::COLUMN_CLIENT_STREETNO] = $value;
                    break;
                case 'clientapartment':
                    $array[self::COLUMN_CLIENT_APARTMENTNO] = $value;
                    break;
            }
        }
        return $array;
    }

    public function setFromCellIterator($data) {
        foreach ($data as $key => $cell) {
            switch ($key) {
                case self::COLUMN_NUMBER:
                    $this->number = trim($cell->getValue());
                    break;
                case self::COLUMN_PLANNEDDATE:
                    //$planneddate = new Zend_Date($cell->getValue(), 'YYYYMMDD');
                    //$this->planneddate = $planneddate->get('YYYY-MM-dd');
                    $value = $cell->getValue();
                    if (!empty($value)) {
                        if (!strtotime($value)) {
                            //$planneddate = new Zend_Date(PHPExcel_Shared_Date::ExcelToPHP($cell));
                            $value = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()));
                        } else {
                            $value = $cell->getValue();
                        }
                        $planneddate = new Zend_Date($value, 'YYYYMMDD');
                        $this->planneddate = $planneddate->get('yyyy-MM-dd');
                        //var_dump($this->planneddate);
                    }
                    break;
                case self::COLUMN_TIMEFROM:
                    $value = $cell->getValue();
                    $value = str_pad($value, 4, '0', STR_PAD_LEFT);
                    $timefrom = new Zend_Date($value, 'HHmm');
                    $this->timefrom = $timefrom->get('HH:mm');//var_dump($cell->getValue(),$this->timefrom);
                    break;
                case self::COLUMN_TIMETILL:
                    $value = $cell->getValue();
                    $value = str_pad($value, 4, '0', STR_PAD_LEFT);
                    $timetill = new Zend_Date($value, 'HHmm');
                    $this->timetill = $timetill->get('HH:mm');//var_dump($cell->getValue(),$this->timetill);
                    break;
                case self::COLUMN_TECHNICIAN:
                    /*$technician = $cell->getValue();
                    @list($firstName, $lastName) = @explode('.', $technician);
                    if (!$firstName) {
                        $firstName = $technician;
                    }
                    if (!$lastName) {
                        $lastName = $technician;
                    }
                    $this->_technician = array(
                        'firstname' => $firstName, 
                        'lastname' => $lastName, 
                        //'email' => $technician
                    );*/
                    $technician = trim($cell->getCalculatedValue());
                    if (!empty($technician)) {
                        //@list($firstName, $lastName) = @explode('.', $technician);
                        //if (!$firstName) {
                        //    $firstName = $technician;
                        //}
                        //if (!$lastName) {
                        //    $lastName = $technician;
                        //}
                        $this->_technician = array(
                            //'firstname' => $firstName, 
                            //'lastname' => $lastName, 
                            //'email' => $technician,
                            'symbol' => $technician
                        );
                    }
                    break;
                case self::COLUMN_CLIENT_NUMBER:
                    $this->_client['number'] = $cell->getValue();
                    break;
                case self::COLUMN_CALENDAR:
                    $this->_calendar = trim($cell->getValue());
                    break;
                case self::COLUMN_SERVICETYPE:
                    $this->_servicetype = trim($cell->getValue());
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
            }
        }
        return $this;
    }

}
