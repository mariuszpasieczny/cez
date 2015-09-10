<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Dictionaries_JSON_Table extends Application_Model_Dictionaries_Table
{
    
    public function proccess($data, $parentid = '0') {
        $this->cacheInClass(false);
        $this->setLazyLoading(true);
        if (!($entry = $this->getAll(array('parentid' => $parentid, 'acronym' => !empty($data['acronym']) ? $data['acronym'] : NULL))->current())) {
            $entry = $this->createRow();
            $entry->setFromArray($data);
            $entry->parentid = $parentid;
            $entry->save();
        }
        foreach ($data as $index => $row) {//if ($parentid){var_dump($data,$index,$row);exit;}
            if (is_array($row)) {
                $this->proccess($row, $entry->id);
            }
        }
    }
    
}