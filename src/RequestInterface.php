<?php


namespace R\Psr7;

use Psr\Http\Message\RequestInterface as MessageRequestInterface;

interface RequestInterface extends MessageRequestInterface
{
    public function HttpAccept();
    public function ContentType();
    public function isAccept(string $content_type);
}
