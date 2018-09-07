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

    public function __construct($headers=[], $body, $version = "1.1")
    {
        $this->headers = new Collection();

        foreach ($headers as $name => $value) {
            foreach (array_map("trim", explode(",", $value)) as $v) {
                $this->headers->add(strtolower($name), [
                    "name" => $name,
                    "value" => $v
                ]);
            }
        }
        $this->body = $body;
        $this->protocolVersion = $version;
    }

    public function __clone(){
        $this->headers=clone $this->headers;
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
        $data = [];
        foreach ($this->headers->all() as $values) {
            foreach ($values as $value) {
                $data[$value["name"]][] = $value["value"];
            }
        }
        return $data;

    }

    public function hasHeader($name)
    {
        return $this->headers->has(strtolower($name));
    }

    public function getHeader($name)
    {
        return array_map(function ($v) {
            return $v["value"];
        }, $this->headers->get(strtolower($name)));
    }

    public function getHeaderLine($name)
    {
        return implode(",",$this->getHeader($name));
    }

    public function withHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers->remove(strtolower($name));
        $clone->headers->add(strtolower($name), ["name" => $name, "value" => $value]);
        return $clone;
    }

    public function withAddedHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers->add(strtolower($name), ["name" => $name, "value" => $value]);
        return $clone;
    }

    public function withoutHeader($name)
    {
        $clone = clone $this;
        $clone->headers->remove(strtolower($name));
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
        $this->headers->remove(strtolower($name));
        $this->headers->add(strtolower($name), ["name" => $name, "value" => $value]);
        return $this;
    }

    public function __toString()
    {
        return (string)$this->body;
    }
}
