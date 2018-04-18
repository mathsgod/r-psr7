<?php

namespace R\Psr7;

class JSONStream extends Stream
{
    protected $_data=null;

    public function __construct($data, $options = []){
        $this->_data=$data;
        parent::__construct(fopen("php://memory","r+"),$options);
    }


    public function write($obj)
    {
        $this->_data=$obj;
    }

    public function getContents()
    {
        return $this->_data;
    }

    public function __toString()
    {
        parent::truncate();
        return json_encode($this->_data, JSON_UNESCAPED_UNICODE);
    }

    public function truncate($size)
    {
        $this->_data=null;
    }
}
