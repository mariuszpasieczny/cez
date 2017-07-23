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
    const COLUMN_REGION = 'I';
    const COLUMN_SERVICETYPE = 'J';
    const COLUMN_CLIENT_CITY = 'K';
    const COLUMN_CLIENT_STREET = 'L';
    const COLUMN_CLIENT_STREETNO = 'M';
    const COLUMN_CLIENT_APARTMENTNO = 'N';
    const COLUMN_TECHNICIANCODE = 'O';
    const COLUMN_DATEFINISHED = 'P';
    const COLUMN_SOLUTIONCODE = 'Q';
    const COLUMN_CANCELLATIONCODE = 'R';
    const COLUMN_MODEMINTERCHANGECODE = 'S';
    const COLUMN_DECODERINTERCHANGECODE = 'T';
    const COLUMN_EQUIPMENTSRELEASED = 'U';
    const COLUMN_MATERIALSRELEASED = 'V';
    const COLUMN_PRODUCTSRELEASED = 'W';
    const COLUMN_PRODUCTSRETURNED = 'X';
    const COLUMN_TECHNICALCOMMENTS = 'Y';
    const COLUMN_COORDINATORCOMMENTS = 'Z';
    const COLUMN_PERFORMED = 'AA';
    const COLUMN_STATUS = 'AB';
    
    const COLUMN_TECHNICIAN_PHONENO = 'AC';
    const COLUMN_TECHNICIAN_EMAIL = 'AD';
    const COLUMN_CLIENT_HOMEPHONE = 'AE';
    const COLUMN_CLIENT_WORKPHONE = 'AF';
    const COLUMN_CLIENT_CELLPHONE = 'AG';
    const COLUMN_COMPLAINTCODE = 'AH';
    const COLUMN_COMMENTS = 'AI';
    const COLUMN_DESCRIPTION = 'AJ';
    const COLUMN_MODIFIEDDATE = 'AK';
    const COLUMN_PRODUCTS = 'AL';
    const COLUMN_SERIALNUMBERS = 'AM';
    const COLUMN_MACNUMBERS = 'AN';
    const COLUMN_EQUIPMENT = 'AO';
    const COLUMN_PARAMETERS = 'AP';
    const COLUMN_COMMENTSREUIRED = 'AQ';
    const COLUMN_ISHORIZON = 'AR';
    const COLUMN_ISDCI = 'AS';


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
                case 'clienthomephone':
                    $array[self::COLUMN_CLIENT_HOMEPHONE] = $value;
                    break;
                case 'clientworkphone':
                    $array[self::COLUMN_CLIENT_WORKPHONE] = $value;
                    break;
                case 'clientcellphone':
                    $array[self::COLUMN_CLIENT_CELLPHONE] = $value;
                    break;
            }
        }
        return $array;
    }
    
}
