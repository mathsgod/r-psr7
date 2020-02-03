<?php

namespace R\Psr7;

use \InvalidArgumentException;
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
{
    protected $method;
    protected $uri;

    public function __construct($method, Uri $uri, array $headers = [], $body = null, $version = '1.1')
    {
        $this->method = $method;
        $this->uri = $uri;

        parent::__construct($headers, $body, $version);
    }

    public function get($name)
    {
        parse_str($this->uri->getQuery(), $arr);
        return $arr[$name];
    }

    public function getRequestTarget()
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }
        $target = $this->uri->getPath();
        if ($this->uri->getQuery()) {
            $target .= '?' . $this->uri->getQuery();
        }
        if (empty($target)) {
            $target = '/';
        }
        return $target;
    }

    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException(
                'Invalid request target provided; must be a string and cannot contain whitespace'
            );
        }
        $clone = clone $this;
        $clone->requestTarget = $requestTarget;
        return $clone;
    }

    /**
     * @return Uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        $clone = clone $this;
        $clone->method = $method;
        return $clone;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $clone = clone $this;
        $clone->uri = $uri;
        if (!$preserveHost) {
            if ($uri->getHost() !== '') {
                $clone->headers->remove("Host");
                $clone->headers->add("Host", $uri->getHost());
            }
        } else {
            if ($uri->getHost() !== '' && (!$this->hasHeader('Host') || $this->getHeaderLine('Host') === '')) {
                $clone->headers->remove("Host");
                $clone->headers->add("Host", $uri->getHost());
            }
        }
        return $clone;
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
