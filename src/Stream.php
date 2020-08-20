<?php

namespace R\Psr7;

use PHP\Psr7\Stream as Psr7Stream;
use Psr\Http\Message\StreamInterface;

class Stream extends Psr7Stream implements StreamInterface
{

    /**
     * Truncates a stream to a given length
     */
    public function truncate(int $size)
    {
        return ftruncate($this->stream, $size);
    }
}
