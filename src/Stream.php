<?php

namespace R\Psr7;

use PHP\Psr7\Stream as Psr7Stream;

class Stream extends Psr7Stream
{

    /**
     * Truncates a stream to a given length
     */
    public function truncate(int $size)
    {
        return ftruncate($this->stream, $size);
    }
}
