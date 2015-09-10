<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Clients_Row extends Application_Db_Table_Row
{
    
    public function __toString() {
        return "{$this->street}"
        . " {$this->streetno}"
        . "/{$this->apartmentno}"
        //. ", {$this->postcode}"
        . " {$this->city}";
    }
    
}