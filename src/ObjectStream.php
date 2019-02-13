<?php

namespace R\Psr7;

class ObjectStream extends Stream
{
    protected $_data = [];

    public function write($obj)
    {
        $this->_data[] = $obj;
    }

    public function getContents()
    {
        parent::truncate($this->getSize());
        foreach ($this->_data as $obj) {
            parent::write((string)$obj);
        }
        return parent::getContents();
    }

    public function truncate($size = null)
    {
        $this->_data = [];
    }
}
