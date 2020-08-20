<?php

namespace R\Psr7;

use PHP\Psr7\Message as Psr7Message;
use Psr\Http\Message\MessageInterface;

class Message extends Psr7Message implements MessageInterface
{
    public function __construct(array $headers = [], $body = null, string $version = "1.1")
    {
        foreach ($headers as $name => $value) {
            $this->headers[$name] = array_map("trim", explode(",", $value));
        }

        if ($body === null) {
            $this->body = new Stream();
        }

        $this->protocolVersion = $version;
    }
}
