<?php

namespace R\Psr7;

use PHP\Psr7\UploadedFile as Psr7UploadedFile;
use Psr\Http\Message\StreamInterface;

class UploadedFile extends Psr7UploadedFile
{
    public function __construct(StreamInterface $stream, $size, $error, string $clientFilename = null, string $clientMediaType = null)
    {
        $this->stream = $stream;
        $this->size = $size;
        $this->error = $error;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }
}
