<?php

class Utils_File_Reader_CSV_RowIterator implements Iterator
{
    /**
     * SplFileObject to iterate
     *
     * @var SplFileObject
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


    /**
     * Create a new row iterator
     *
     * @param    SplFileObject    $subject    The file to iterate over
     * @param    integer                $startRow    The row number at which to start iterating
     * @param    integer                $endRow        Optionally, the row number at which to stop iterating
     */
    public function __construct(SplFileObject $subject = null, $startRow = 1, $endRow = null)
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
        $this->subject->rewind();

        return $this;
    }

    /**
     * Set the row pointer to the selected row
     *
     * @param integer    $row    The row number to set the current pointer at
     * @return Utils_File_Reader_CSV_RowIterator
     * @throws Exception
     */
    public function seek($row = 1)
    {
        $this->subject->seek();

        return $this;
    }

    /**
     * Rewind the iterator to the starting row
     */
    public function rewind()
    {
        $this->subject->rewind();
    }

    /**
     * Return the current row in this worksheet
     *
     * @return string|array
     */
    public function current()
    {
        if (current($this->subject->current()) === null) {
            return false;
        }
        return explode(';', current($this->subject->current()));
    }

    /**
     * Return the current iterator key
     *
     * @return int
     */
    public function key()
    {
        return $this->subject->key();
    }

    /**
     * Set the iterator to its next value
     */
    public function next()
    {
        $this->subject->next();
    }

    /**
     * Indicate if more rows exist in the worksheet range of rows that we're iterating
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->subject->valid();
    }
}
