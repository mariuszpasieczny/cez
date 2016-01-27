<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Db_Table_Rowset extends Zend_Db_Table_Rowset
{
    
    public function find($value, $column = null) {
        if (!$column) {
            $info = $this->getTable()->info();
            $column = $info['primary'];
        }
        if (!is_array($column)) {
            $column = (array)$column;
        }
        if (!is_array($value)) {
            $value = (array)$value;
        }
        foreach ($this as $row) {
            if (sizeof(@array_intersect_assoc(array_map('strtoupper', $row->toArray()), @array_combine($column, array_map('strtoupper', $value)))) == sizeof($column)) {
                return $row;
            }
        }
        return null;
    }
    
    public function filter($params) {
        $rows = array();//var_dump($params,$this->toArray());
        foreach ($this as $row) {
            foreach ($params as $key => $value) {
                if (strpos($value, 'LIKE') !== false && strpos($row->{$key}, trim(str_replace('LIKE', '', $value))) === false) {
                    continue 2;
                }
                if (strpos($value, '>=') !== false && !(trim(str_replace('>=', '', $value)) >= $row->{$key})) {
                    continue 2;
                }
                if (strpos($value, '<=') !== false && !(trim(str_replace('<=', '', $value)) <= $row->{$key})) {
                    continue 2;
                }
                if (!(trim(str_replace('=', '', $value)) == $row->{$key})) {
                    continue 2;
                }
            }
            $rows[] = $row->toArray();
            //$rows[] = $row;
        }//var_dump($rows);exit;
        //return $rows;
        
        $rowsetClass = get_class($this);
        
        $data = array(
            'table' => $this->getTable(),
            'data' => $rows,
            'readOnly' => false,
            'rowClass' => $rowsetClass,
            'stored' => true
        );

        if (!class_exists($rowsetClass)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($rowsetClass);
        }
        return new $rowsetClass($data);
    }
    
    public function add($row) {
        array_push($this->_data, $row->toArray());
        $this->_count++;
        return $this;
    }
    
}