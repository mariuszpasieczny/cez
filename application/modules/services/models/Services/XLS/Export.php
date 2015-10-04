<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_XLS_Export extends Application_Model_Services_Service
{
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
    const COLUMN_SOLUTIONCODE = 'P';
    const COLUMN_CANCELLATIONCODE = 'Q';
    const COLUMN_MODEMINTERCHANGECODE = 'R';
    const COLUMN_DECODERINTERCHANGECODE = 'S';
    const COLUMN_PRODUCTSRELEASED = 'T';
    const COLUMN_PRODUCTSRETURNED = 'U';
    const COLUMN_TECHNICALCOMMENTS = 'V';
    const COLUMN_COORDINATORCOMMENTS = 'W';
    const COLUMN_PERFORMED = 'X';
    const COLUMN_STATUS = 'Y';

    public function toXlsArray() {//var_dump($this->toArray());exit;
        $array = array();
        foreach ($this->_data as $key => $value) {
            switch ($key) {
                default:
                    $column = 'COLUMN_' . strtoupper($key);
                    //$array[self::$column] = $value;
                    $r = new ReflectionClass('Application_Model_Services_XLS_Export');
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
    
}
