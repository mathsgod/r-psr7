<?php

namespace R\Psr7;

use PHP\Psr7\Message as Psr7Message;
use Psr\Http\Message\MessageInterface;

class Message extends Psr7Message implements MessageInterface
{

    public function __toString()
    {
        return (string)$this->body;
    }
}
