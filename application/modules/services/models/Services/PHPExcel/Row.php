<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_PHPExcel_Row extends Application_Model_Services_Row
{
    const COLUMN_SERVICETYPE = 'A';
    const COLUMN_PLANNEDDATE = 'B';
    const COLUMN_FROM = 'C';
    const COLUMN_TILL = 'D';
    const COLUMN_CLIENT_NUMBER = 'F';
    const COLUMN_CLIENT_POSTCODE = 'G';
    const COLUMN_CLIENT_CITY = 'H';
    const COLUMN_CLIENT_STREET = 'I';
    const COLUMN_CLIENT_STREETNO = 'J';
    const COLUMN_CLIENT_APARTMENTNO = 'K';
    const COLUMN_REGION = 'L';
    const COLUMN_NUMBER = 'R';
    const COLUMN_LABORCODE = 'S';
    const COLUMN_COMPLAINTCODE = 'T';
    const COLUMN_CALENDAR = 'V';
    const COLUMN_COMMENTS = 'X';
    const COLUMN_AREA = 'Y';
    const COLUMN_SLOTS = 'Z';
    const COLUMN_PRODUCTS = 'AA';
    const COLUMN_SERIALNUMBER = 'AB';
    const COLUMN_MACNUMBER = 'AC';
    const COLUMN_TECHNICIAN = 'W';
    
    public function setFromArray($data) {
        foreach ($data as $key => $cell) {
            switch ($key) {
                case 'A':
                    $this->_servicetype = $cell->getCalculatedValue();
                    break;
                case 'B':
                    if (!strtotime($cell->getCalculatedValue())) {
                        $planneddate = new Zend_Date(PHPExcel_Shared_Date::ExcelToPHP($cell));
                        $value = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cell->getCalculatedValue()));
                    } else {
                        $value = $cell->getCalculatedValue();
                    }
                    $planneddate = new Zend_Date($value);
                    $this->planneddate = $planneddate->get('yyyy-MM-dd');
                    //var_dump($this->number,$cell->getValue(),$value,$this->planneddate);
                    break;
                case 'C':
                    $timefrom = new Zend_Date($cell->getCalculatedValue(), 'Hmm');
                    $this->timefrom = $timefrom->get('H:mm');
                    break;
                case 'D':
                    $timetill = new Zend_Date($cell->getCalculatedValue(), 'Hmm');
                    $this->timetill = $timetill->get('H:mm');
                    break;
                case 'F':
                    $this->_client['number'] = $cell->getCalculatedValue();
                    break;
                case 'G':
                    $this->_client['postcode'] = $cell->getCalculatedValue();
                    break;
                case 'H':
                    $this->_client['city'] = $cell->getCalculatedValue();
                    break;
                case 'I':
                    $this->_client['street'] = $cell->getCalculatedValue();
                    break;
                case 'J':
                    $this->_client['streetno'] = $cell->getCalculatedValue();
                    break;
                case 'K':
                    $this->_client['apartmentno'] = $cell->getCalculatedValue();
                    break;
                case 'L':
                    $this->_region = $cell->getCalculatedValue();
                    break;
                case 'R':
                    $this->number = $cell->getCalculatedValue();
                    break;
                case 'S':
                    $this->_laborcode = $cell->getCalculatedValue();
                    break;
                case 'T':
                    $this->_complaintcode = $cell->getCalculatedValue();
                    break;
                case 'V':
                    $this->_calendar = $cell->getCalculatedValue();
                    break;
                case 'X':
                    $this->comments = $cell->getCalculatedValue();
                    break;
                case 'Y':
                    $this->_area = $cell->getCalculatedValue();
                    break;
                case 'Z':
                    $this->slots = $cell->getCalculatedValue();
                    break;
                case 'AA':
                    $this->products = $cell->getCalculatedValue();
                    break;
                case 'AB':
                    $this->serialnumbers = $cell->getCalculatedValue();
                    break;
                case 'AC':
                    $this->macnumbers = $cell->getCalculatedValue();
                    break;
                case 'W':
                    $technician = $cell->getCalculatedValue();
                    list($firstName, $lastName) = explode('.', $technician);
                    if (!$firstName) {
                        $firstName = $technician;
                    }
                    if (!$lastName) {
                        $lastName = $technician;
                    }
                    $this->_technician = array('firstname' => $firstName, 'lastname' => $lastName, 'email' => $technician);
                    break;
            }
        }
        return $this;
    }
    
}
