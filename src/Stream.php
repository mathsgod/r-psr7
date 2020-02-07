<?php

namespace R\Psr7;

use RuntimeException;
use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{

    protected $stream;
    protected $meta;
    protected $readable;
    protected $writable;
    protected $seekable;
    protected $size;
    protected $isPipe;

    const READ_WRITE_HASH = [
        'read' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true,
            'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a+' => true
        ],
        'write' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true,
            'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true,
            'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true
        ]
    ];

    public function __construct($stream = null)
    {
        if ($stream === null) {
            $stream = fopen("php://memory", "r+");
        } elseif (is_string($stream)) {
            $str = $stream;
            $stream = fopen("php://memory", "r+");
            fwrite($stream, $str);
        }

        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('Stream must be a resource');
        }

        $this->stream = $stream;
        $meta = stream_get_meta_data($this->stream);
        $this->seekable = $meta['seekable'];
        $this->readable = isset(self::READ_WRITE_HASH["read"][$meta['mode']]);
        $this->writable = isset(self::READ_WRITE_HASH["write"][$meta['mode']]);
        $this->uri = $this->getMetadata('uri');
        $mode = fstat($this->stream)["mode"];
        $this->isPipe = ($mode & 0010000) !== 0;
    }

    public function __toString()
    {
        if (!$this->stream) {
            return "";
        }
        try {
            $this->rewind();
        } catch (RuntimeException $e) {
        }

        try {
            return $this->getContents();
        } catch (RuntimeException $e) {
        }

        return "";
    }

    public function close()
    {
        if ($this->stream) {
            if ($this->isPipe) {
                pclose($this->stream);
            } else {
                fclose($this->stream);
            }
        }
        $this->detach();
    }

    public function detach()
    {
        if (!isset($this->stream)) {
            return null;
        }

        $oldResource = $this->stream;
        $this->stream = null;
        $this->meta = null;
        $this->readable = null;
        $this->writable = null;
        $this->seekable = null;
        $this->size = null;
        $this->isPipe = null;
        return $oldResource;
    }

    public function getSize()
    {
        if ($this->size !== null) {
            return $this->size;
        }
        if (!isset($this->stream)) {
            return null;
        }
        // Clear the stat cache if the stream has a URI
        if ($this->uri) {
            clearstatcache(true, $this->uri);
        }
        $stats = fstat($this->stream);
        if (isset($stats['size'])) {
            $this->size = $stats['size'];
            return $this->size;
        }
        return null;
    }

    public function tell()
    {
        if (!$this->stream || ($position = ftell($this->stream)) === false || $this->isPipe) {
            throw new RuntimeException('Could not get the position of the pointer in stream');
        }
        return $position;
    }

    public function eof()
    {
        return $this->stream ? feof($this->stream) : true;
    }

    public function isSeekable()
    {
        return $this->seekable;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        // Note that fseek returns 0 on success!
        if (!$this->isSeekable() || fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException('Could not seek in stream');
        }
    }


    public function rewind()
    {
        if (!$this->isSeekable() || rewind($this->stream) === false) {
            throw new RuntimeException('Could not rewind stream');
        }
    }

    public function isWritable()
    {
        return $this->writable;
    }

    public function write($string)
    {
        if (!$this->isWritable() || ($written = fwrite($this->stream, $string)) === false) {
            throw new RuntimeException('Could not write to stream');
        }
        // reset size so that it will be recalculated on next call to getSize()
        $this->size = null;
        return $written;
    }

    public function isReadable()
    {
        return $this->readable;
    }

    public function read($length)
    {
        if (!$this->isReadable() || ($data = fread($this->stream, $length)) === false) {
            throw new RuntimeException('Could not read from stream');
        }
        return $data;
    }

    public function getContents()
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }
        fseek($this->stream, 0);
        $contents = stream_get_contents($this->stream);
        if ($contents === false) {
            throw new \RuntimeException('Unable to read stream contents');
        }
        return $contents;
    }

    public function getMetadata($key = null)
    {
        $this->meta = stream_get_meta_data($this->stream);
        if (is_null($key) === true) {
            return $this->meta;
        }
        return isset($this->meta[$key]) ? $this->meta[$key] : null;
    }

    /**
     * Truncates a stream to a given length
     */
    public function truncate(int $size)
    {
        return ftruncate($this->stream, $size);
    }
}
