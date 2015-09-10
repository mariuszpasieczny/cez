<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Db_Table_PHPExcel_Rowset extends Zend_Db_Table_Rowset_Abstract
{
    
    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        // set the count of rows
        $this->_count = count($config['count']);
    }
    
    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * Required by interface Iterator.
     *
     * @return Zend_Db_Table_Row_Abstract current element from the collection
     */
    public function current()
    {
        if ($this->valid() === false) {
            return null;
        }

        // do we already have a row object for this position?
        if (empty($this->_rows[$this->_pointer])) {
            $row = $this->_data->current();
            $this->_rows[$this->_pointer] = new $this->_rowClass(
                array(
                    'table'    => $this->_table,
                    'data'     => $this->_data->current()->getCellIterator(),
                    'stored'   => $this->_stored,
                    'readOnly' => $this->_readOnly
                )
            );
        }

        // return the row object
        return $this->_rows[$this->_pointer];
    }
    
}