<?php

namespace R\Psr7;

class JSONStream extends Stream
{
    public function __construct($data = null, $options = [])
    {
        parent::__construct();
        $this->seekable = false;
        parent::write(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function write($obj)
    {
        parent::truncate(0);
        return parent::write(json_encode($obj, JSON_UNESCAPED_UNICODE));
    }
}
