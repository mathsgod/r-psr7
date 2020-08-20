<?php

namespace R\Psr7;

use \Psr\Http\Message\ResponseInterface;
use PHP\Psr7\ResponseTrait;

class Response extends Message implements ResponseInterface
{

    use ResponseTrait;

    public function __construct(int $status = 200, array $headers = [], $body = null, string $version = '1.1')
    {
        $this->status = $status;
        parent::__construct($headers, $body, $version);
    }
}
