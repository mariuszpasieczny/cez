<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Db_Table_PHPExcel_Row extends Zend_Db_Table_Row_Abstract
{
    
    /**
     * Constructor.
     *
     * Supported params for $config are:-
     * - table       = class name or object of type Zend_Db_Table_Abstract
     * - data        = values of columns in this row.
     *
     * @param  array $config OPTIONAL Array of user-specified config options.
     * @return void
     * @throws Zend_Db_Table_Row_Exception
     */
    public function __construct(array $config = array())
    {
        if (isset($config['table']) && $config['table'] instanceof Zend_Db_Table_Abstract) {
            $this->_table = $config['table'];
            $this->_tableClass = get_class($this->_table);
        } else if ($this->_tableClass !== null) {
            if (!class_exists($this->_tableClass)) {
                require_once 'Zend/Loader.php';
                Zend_Loader::loadClass($this->_tableClass);
            }
            $this->_table = new $this->_tableClass();
        }

        if (isset($config['data'])) {
            if (!($config['data'] instanceof PHPExcel_Worksheet_CellIterator)) {
                require_once 'Zend/Db/Table/Row/Exception.php';
                throw new Zend_Db_Table_Row_Exception('Data must be a PHPExcel_Worksheet_CellIterator');
            }
            $this->_data = $config['data'];
        }
        if (isset($config['stored']) && $config['stored'] === true) {
            $this->_cleanData = $this->_data;
        }

        if (isset($config['readOnly']) && $config['readOnly'] === true) {
            $this->setReadOnly(true);
        }

        // Retrieve primary keys from table schema
        if (($table = $this->_getTable())) {
            $info = $table->info();
            $this->_primary = (array) $info['primary'];
        }

        $this->init();
    }
    
    /**
     * Sets all data in the row from an array.
     *
     * @param  array $data
     * @return Zend_Db_Table_Row_Abstract Provides a fluent interface
     */
    public function setFromCellIterator($data)
    {
        $info = $this->getTable()->info();
        $cols = array_keys($info['metadata']);
        foreach ($data as $cell) {
            if (!in_array($cell->getColumn(), $cols)) {
                continue;
            }
            $this->_data[$cell->getColumn()] = $cell->getCalculatedValue();
        }

        return $this;
    }
    
    /**
     * Returns the column/value data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $info = $this->getTable()->info();
        $cols = array_keys($info['metadata']);
        $data = array();
        foreach ($this->_data as $cell) {
            if (!in_array($cell->getColumn(), $cols)) {
                continue;
            }
            if ($cell->isFormula()) {
                continue;
            }var_dump($cell->getDataType(),$cell->__toString());
            $data[$cell->getColumn()] = $cell->__toString();
        }
        return $data;
    }
    
}