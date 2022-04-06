<?php

declare(strict_types=1);
//error_reporting(E_ALL && ~E_WARNING);
use PHPUnit\Framework\TestCase;

use R\Psr7\JSONStream;

final class JSONStreamTest extends TestCase
{
    public function test_create()
    {
        $s = new JSONStream(["a" => 1]);
        $this->assertInstanceOf(JSONStream::class, $s);
    }

    public function testToString()
    {
        $s = new JSONStream(["a" => 1]);
        $this->assertEquals('{"a":1}', (string) $s);
    }


    public function testToWrite()
    {
        $s = new JSONStream(["a" => 1]);
        $s->write(["b" => 2]);
        $this->assertEquals('{"a":1,"b":2}', (string) $s);
    }
}