<?php
namespace R\Psr7;

use \InvalidArgumentException;
use \Psr\Http\Message\MessageInterface;
use \Psr\Http\Message\StreamInterface;

class Message implements MessageInterface
{
    protected $protocolVersion;

    protected $headers;

    protected $body;

    public function __construct($headers, $body, $version = "1.1")
    {
        foreach ($headers as $name => $value) {
            $this->headers[$name]=array_map("trim", explode(",", $value));
        }

        $this->body=$body;
        $this->protocolVersion=$version;
    }
    
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
/*        if (!isset(self::$validProtocolVersions[$version])) {
        throw new InvalidArgumentException(
            'Invalid HTTP version. Must be one of: '
            . implode(', ', array_keys(self::$validProtocolVersions))
        );
        }*/
        $clone = clone $this;
        $clone->protocolVersion = $version;
        return $clone;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
    
    public function hasHeader($name)
    {
        return (bool)$this->headers[$name];
    }

    public function getHeader($name)
    {
        return $this->headers[$name];
    }

    public function getHeaderLine($name)
    {
        return implode(',', $this->headers["name"]);
    }

    public function withHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers[$name]=[$value];
        return $clone;
    }

    public function withAddedHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers[$name][]=$value;
        return $clone;
    }

    public function withoutHeader($name)
    {
        $clone = clone $this;
        unset($clone->headers[$name]);
        return $clone;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        // TODO: Test for invalid body?
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }

    //-------------------

    public function setHeader($name, $value)
    {
        
        $this->headers[$name]=[$value];
        return $this;
    }

    public function __toString(){
        return (string)$this->body;
    }
}
