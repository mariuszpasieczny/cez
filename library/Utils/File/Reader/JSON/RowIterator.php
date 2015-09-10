<?php

class Utils_File_Reader_JSON_RowIterator implements Iterator
{
    /**
     * array to iterate
     *
     * @var array
     */
    private $subject;

    /**
     * Start position
     *
     * @var int
     */
    private $startRow = 1;


    /**
     * End position
     *
     * @var int
     */
    private $endRow = 1;
    
    private $_pointer;
    
    private $_count;


    /**
     * Create a new row iterator
     *
     * @param    SplFileObject    $subject    The file to iterate over
     * @param    integer                $level    The row number at which to start iterating
     */
    public function __construct(array $subject = null, $level = 1)
    {
        // Set subject
        $this->subject = $subject;
        $this->resetStart($startRow);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->subject);
    }

    /**
     * (Re)Set the start row and the current row pointer
     *
     * @param integer    $startRow    The row number at which to start iterating
     * @return Utils_File_Reader_CSV_RowIterator
     */
    public function resetStart($startRow = 1)
    {
        $this->startRow = $startRow;
        $this->_pointer = 0;

        return $this;
    }

    public function count()
    {
        return $this->_count;
    }
    
    public function seek($row = 1)
    {
        $row = (int) $row;
        if ($row < 0 || $row >= $this->_count) {
            require_once 'Zend/Db/Table/Rowset/Exception.php';
            throw new Zend_Db_Table_Rowset_Exception("Illegal index $row");
        }
        $this->_pointer = $row;
        return $this;
    }

    /**
     * Rewind the iterator to the starting row
     */
    public function rewind()
    {
        $this->_pointer = 0;
        return $this;
    }

    /**
     * Return the current row in this worksheet
     *
     * @return string|array
     */
    public function current()
    {
        if ($this->valid() === false) {
            return null;
        }var_dump($this->_pointer);exit;

        // do we already have a row object for this position?
        if (empty($this->_rows[$this->_pointer])) {
            $this->_rows[$this->_pointer] = new $this->_rowClass(
                array(
                    'table'    => $this->_table,
                    'data'     => $this->_data[$this->_pointer],
                    'stored'   => $this->_stored,
                    'readOnly' => $this->_readOnly
                )
            );
        }

        // return the row object
        return $this->_rows[$this->_pointer];
    }

    /**
     * Return the current iterator key
     *
     * @return int
     */
    public function key()
    {
        return $this->_pointer;
    }

    /**
     * Set the iterator to its next value
     */
    public function next()
    {
        ++$this->_pointer;
    }

    /**
     * Indicate if more rows exist in the worksheet range of rows that we're iterating
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_pointer < $this->_count;
    }
}
