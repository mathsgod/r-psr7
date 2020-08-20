<?php

namespace R\Psr7;

use PHP\Psr7\UploadedFile as Psr7UploadedFile;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile extends Psr7UploadedFile implements UploadedFileInterface
{
}
