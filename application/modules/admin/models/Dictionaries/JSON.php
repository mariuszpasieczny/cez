<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Dictionaries_JSON extends Application_Model_Dictionaries_Row
{
    
    const COLUMN_ACRONYM = 'M';
    const COLUMN_NAME = 'N';
    
    public function setFromCellIterator($data) {
        foreach ($data as $key => $cell) {
            switch ($key) {
                case self::COLUMN_ACRONYM:
                    $this->acronym = $cell->getValue();
                    break;
                case self::COLUMN_NAME:
                    $this->name = $cell->getValue();
                    break;
            }
        }
        return $this;
    }
    
}