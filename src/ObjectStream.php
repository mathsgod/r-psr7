<?php

namespace R\Psr7;

use Psr\Http\Message\StreamInterface;

class ObjectStream extends Stream implements StreamInterface
{
    protected $_data = [];

    public function write($obj)
    {
        $this->_data[] = $obj;
    }

    public function getContents()
    {
        parent::truncate(0);
        foreach ($this->_data as $obj) {
            parent::write((string) $obj);
        }
        return parent::getContents();
    }

    public function truncate(int $size)
    {
        $this->_data = [];
        return true;
    }
}
