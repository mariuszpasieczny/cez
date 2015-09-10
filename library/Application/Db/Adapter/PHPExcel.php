<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '/PHPExcel/PHPExcel.php';

class Application_Db_Adapter_PHPExcel extends Zend_Db_Adapter_Abstract {
    
    /**
     * Check for config options that are mandatory.
     * Throw exceptions if any are missing.
     *
     * @param array $config
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _checkRequiredOptions(array $config)
    {
        // we need at least a dbname
        if (! array_key_exists('filename', $config)) {
            /** @see Zend_Db_Adapter_Exception */
            require_once 'Zend/Db/Adapter/Exception.php';
            throw new Zend_Db_Adapter_Exception("Configuration array must have a key for 'filename' that names the database instance");
        }
    }
    
    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables() {}

    /**
     * Returns the column descriptions for a table.
     *
     * The return value is an associative array keyed by the column name,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME => string; name of database or schema
     * TABLE_NAME  => string;
     * COLUMN_NAME => string; column name
     * COLUMN_POSITION => number; ordinal position of column in table
     * DATA_TYPE   => string; SQL datatype name of column
     * DEFAULT     => string; default expression of column, null if none
     * NULLABLE    => boolean; true if column can have nulls
     * LENGTH      => number; length of CHAR/VARCHAR
     * SCALE       => number; scale of NUMERIC/DECIMAL
     * PRECISION   => number; precision of NUMERIC/DECIMAL
     * UNSIGNED    => boolean; unsigned property of an integer type
     * PRIMARY     => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null) {
        $this->_connect();
        
        $highestColumn = $this->getConnection()->getActiveSheet()->getHighestColumn();
        $columnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $iterator = $this->getConnection()->getActiveSheet()->getRowIterator();
        $iterator->next();
        $row = $iterator->current();
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $desc = array();
        $i = 1;
        foreach ($cellIterator as $cell) {
            $value = $cell->getCalculatedValue();
            if (!is_null($value)) {
                $desc[$this->foldCase($cell->getColumn())] = array(
                    'SCHEMA_NAME'      => null, // @todo
                    'TABLE_NAME'       => $this->foldCase($tableName),
                    'COLUMN_NAME'      => $value,
                    'COLUMN_POSITION'  => $i,
                    //'DATA_TYPE'        => $row[$type],
                    //'DEFAULT'          => $row[$default],
                    //'NULLABLE'         => (bool) ($row[$null] == 'YES'),
                    //'LENGTH'           => $length,
                    //'SCALE'            => $scale,
                    //'PRECISION'        => $precision,
                    //'UNSIGNED'         => $unsigned,
                    //'PRIMARY'          => $primary,
                    //'PRIMARY_POSITION' => $primaryPosition,
                    //'IDENTITY'         => $identity
                );
                ++$i;
            }
        }
        return $desc;
    }
    
    /**
     * Fetches all SQL result rows as a sequential array.
     * Uses the current fetchMode for the adapter.
     *
     * @param string|Zend_Db_Select $sql  An SQL SELECT statement.
     * @param mixed                 $bind Data to bind into SELECT placeholders.
     * @param mixed                 $fetchMode Override current fetch mode.
     * @return PHPExcel_Worksheet
     */
    public function fetchAll($sql, $bind = array(), $fetchMode = null)
    {
        return $this->getConnection()->getActiveSheet();
    }

    /**
     * Creates a connection to the database.
     *
     * @return void
     */
    protected function _connect() {
        
        if ($this->_connection) {
            return;
        }
        
        if (!$this->_config['options']['readerType']) {
            $this->_config['options']['readerType'] = 'Excel2007';
        }
        if (!$this->_config['options']['readOnly']) {
            $this->_config['options']['readOnly'] = false;
        }

        $objReader = PHPExcel_IOFactory::createReader($this->_config['options']['readerType']);
        $objReader->setReadDataOnly($this->_config['options']['readOnly']);
        $this->_connection = $objReader->load($this->_config['filename']);
    }

    /**
     * Test if a connection is active
     *
     * @return boolean
     */
    public function isConnected() {}

    /**
     * Force the connection to close.
     *
     * @return void
     */
    public function closeConnection() {}

    /**
     * Prepare a statement and return a PDOStatement-like object.
     *
     * @param string|Zend_Db_Select $sql SQL query
     * @return Zend_Db_Statement|PDOStatement
     */
    public function prepare($sql) {}

    /**
     * Gets the last ID generated automatically by an IDENTITY/AUTOINCREMENT column.
     *
     * As a convention, on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2), this method forms the name of a sequence
     * from the arguments and returns the last id generated by that sequence.
     * On RDBMS brands that support IDENTITY/AUTOINCREMENT columns, this method
     * returns the last value generated for such a column, and the table name
     * argument is disregarded.
     *
     * @param string $tableName   OPTIONAL Name of table.
     * @param string $primaryKey  OPTIONAL Name of primary key column.
     * @return string
     */
    public function lastInsertId($tableName = null, $primaryKey = null) {}

    /**
     * Begin a transaction.
     */
    protected function _beginTransaction() {}

    /**
     * Commit a transaction.
     */
    protected function _commit() {}

    /**
     * Roll-back a transaction.
     */
    protected function _rollBack() {}

    /**
     * Set the fetch mode.
     *
     * @param integer $mode
     * @return void
     * @throws Zend_Db_Adapter_Exception
     */
    public function setFetchMode($mode) {}

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param mixed $sql
     * @param integer $count
     * @param integer $offset
     * @return string
     */
    public function limit($sql, $count, $offset = 0) {}

    /**
     * Check if the adapter supports real SQL parameters.
     *
     * @param string $type 'positional' or 'named'
     * @return bool
     */
    public function supportsParameters($type) {}

    /**
     * Retrieve server version in PHP style
     *
     * @return string
     */
    public function getServerVersion() {}
    
}