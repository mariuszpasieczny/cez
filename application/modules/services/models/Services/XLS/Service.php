<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_XLS_Service extends Application_Model_Services_Service
{
    const COLUMN_SERVICETYPE = 'A';
    const COLUMN_PLANNEDDATE = 'B';
    const COLUMN_TIMEFROM = 'C';
    const COLUMN_TIMETILL = 'D';
    const COLUMN_SYSTEM = 'E';
    const COLUMN_CLIENT_NUMBER = 'F';
    const COLUMN_CLIENT_POSTCODE = 'G';
    const COLUMN_CLIENT_CITY = 'H';
    const COLUMN_CLIENT_STREET = 'I';
    const COLUMN_CLIENT_STREETNO = 'J';
    const COLUMN_CLIENT_APARTMENTNO = 'K';
    const COLUMN_CLIENT_HOMEPHONE = 'M';
    const COLUMN_CLIENT_WORKPHONE = 'N';
    const COLUMN_CLIENT_CELLPHONE = 'O';
    const COLUMN_REGION = 'L';
    const COLUMN_BLOCKADECODE = 'Q';
    const COLUMN_NUMBER = 'R';
    const COLUMN_LABORCODE = 'S';
    const COLUMN_COMPLAINTCODE = 'T';
    const COLUMN_CALENDAR = 'V';
    const COLUMN_COMMENTS = 'X';
    const COLUMN_AREA = 'Y';
    const COLUMN_SLOTS = 'Z';
    const COLUMN_TECHNICIAN = 'W';
    const COLUMN_PRODUCTS = 'AA';
    const COLUMN_SERIALNUMBERS = 'AB';
    const COLUMN_MACNUMBERS = 'AC';
    const COLUMN_DATEFINISHED = 'AK';
    const COLUMN_TECHNICIANCODE = 'AL';
    const COLUMN_ERRORCODE = 'AM';
    const COLUMN_SOLUTIONCODE = 'AN';
    const COLUMN_CANCELLATIONCODE = 'AP';
    const COLUMN_MODEMINTERCHANGECODE = 'AR';
    const COLUMN_DECODERINTERCHANGECODE = 'AT';
    const COLUMN_TECHNICALCOMMENTS = 'AX';
    const COLUMN_PRODUCTSRETURNED = 'AV';
    const COLUMN_PRODUCTSRELEASED = 'AW';
    const COLUMN_TECHNICALCOMMENTSREQUIRED = 'BH';
    
    public function toXlsArray() {
        $array = array();
        foreach ($this->_data as $key => $value) {
            switch ($key) {
                default:
                    $column = 'COLUMN_' . strtoupper($key);
                    //$array[self::$column] = $value;
                    $r = new ReflectionClass('Application_Model_Services_XLS_Service');
                    $id = $r->getConstant($column);
                    if ($id) {
                        $array[$id] = $value;
                    }
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
                case 'clientpostcode':
                    $array[self::COLUMN_CLIENT_POSTCODE] = $value;
                    break;
            }
        }
        return $array;
    }
    
    public function setFromCellIterator($data) {
        foreach ($data as $key => $cell) {
            switch ($key) {
                case self::COLUMN_TECHNICALCOMMENTSREQUIRED:
                    //var_dump($cell->getValue());exit;
                    break;
                case self::COLUMN_SERVICETYPE:
                    $this->_servicetype = strtoupper(trim($cell->getValue()));
                    break;
                case self::COLUMN_PLANNEDDATE:
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
                case self::COLUMN_CLIENT_NUMBER:
                    $this->_client['number'] = $cell->getValue();
                    break;
                case self::COLUMN_CLIENT_POSTCODE:
                    $this->_client['postcode'] = $cell->getValue();
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
                case self::COLUMN_CLIENT_HOMEPHONE:
                    $this->_client['homephone'] = $cell->getValue();
                    break;
                case self::COLUMN_CLIENT_WORKPHONE:
                    $this->_client['workphone'] = $cell->getValue();
                    break;
                case self::COLUMN_CLIENT_CELLPHONE:
                    $this->_client['cellphone'] = $cell->getValue();
                    break;
                case self::COLUMN_REGION:
                    $this->_region = strtoupper(trim($cell->getValue()));
                    break;
                case self::COLUMN_NUMBER:
                    $this->number = trim($cell->getValue());
                    break;
                case self::COLUMN_SYSTEM:
                    $this->_system = strtoupper(trim($cell->getValue()));
                    break;
                case self::COLUMN_LABORCODE:
                    $this->_laborcode = strtoupper(trim($cell->getValue()));
                    break;
                case self::COLUMN_BLOCKADECODE:
                    $this->_blockadecode = strtoupper(trim($cell->getValue()));
                    break;
                case self::COLUMN_COMPLAINTCODE:
                    $this->_complaintcode = strtoupper(trim($cell->getValue()));
                    break;
                case self::COLUMN_CALENDAR:
                    $this->_calendar = strtoupper(trim($cell->getValue()));
                    break;
                case self::COLUMN_COMMENTS:
                    $this->comments = $cell->getValue();
                    break;
                case self::COLUMN_AREA:
                    $this->_area = strtoupper(trim($cell->getValue()));
                    break;
                case self::COLUMN_SLOTS:
                    $this->slots = $cell->getValue();
                    break;
                case self::COLUMN_PRODUCTS:
                    $this->products = $cell->getValue();
                    break;
                case self::COLUMN_SERIALNUMBERS:
                    $this->serialnumbers = $cell->getValue();
                    break;
                case self::COLUMN_MACNUMBERS:
                    $this->macnumbers = $cell->getValue();
                    break;
                case self::COLUMN_TECHNICIANCODE:
                    $technician = strtoupper(trim($cell->getCalculatedValue()));
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
            }
        }
        return $this;
    }
    
}
