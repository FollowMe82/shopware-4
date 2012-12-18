<?php
abstract class Shopware_Components_DbExport_Abstract implements SeekableIterator
{
    /**
     * @var PDO
     */
    protected $db;
    protected $step = 10;
    protected $position;
    protected $table;
    protected $current;

    public function __construct(PDO $db, $table = null)
    {
        $this->db = $db;
        if ($table !== null) {
            $this->setTable($table);
        }
    }

    public function setTable($table)
    {
        $this->table = $table;
        $this->rewind();
    }

    public function seek($position)
    {
        $this->position = (int)$position;
        $this->fetch();
    }

    public function rewind()
    {
        $this->position = 0;
        $this->fetch();
    }

    public function current()
    {
        return $this->current;
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
        $this->fetch();
    }

    public function valid()
    {
        return $this->current !== null;
    }

    public function fetch()
    {
        if (!$this->position) {
            $this->current = $this->getTable();
        } else {
            $offset = $this->position == 1 ? 0 : ($this->position - 1) * $this->step;
            $this->current = $this->getTableData($this->step, $offset);
        }
    }

    public function each()
    {
        if (!$this->valid()) {
            return false;
        }
        $result = array($this->key(), $this->current());
        $this->next();
        return $result;
    }

    abstract public function getTableData($limit, $offset = 0);

    abstract public function getTable($newTable = null);

    abstract public function listTables();
}