<?php

namespace R\Psr7;

use PHP\Psr7\RequestTrait;
use \Psr\Http\Message\RequestInterface;


class Request extends Message implements RequestInterface
{
    use RequestTrait;

    public function __construct(string $method, Uri $uri, array $headers = [], $body = null, $version = '1.1')
    {
        $this->method = $method;
        $this->uri = $uri;

        parent::__construct($headers, $body, $version);
    }

    //-----
    public function HttpAccept()
    {

        $accepts = [];
        foreach ($this->getHeader("Accept") as $a) {
            $ss = explode(";", $a, 2);
            $accepts[] = ["media" => $ss[0], "params" => $ss[1]];
        }
        return $accepts;
    }

    public function ContentType()
    {
        $s = explode(";", $this->serverParams["CONTENT_TYPE"]);

        return strtolower($s[0]);
    }

    public function isAccept($content_type)
    {
        foreach ($this->HttpAccept() as $accept) {
            if ($accept["media"] == $content_type) {
                return true;
            }
        }
        return false;
    }
}
