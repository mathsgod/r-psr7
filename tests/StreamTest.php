<?
declare (strict_types = 1);
//error_reporting(E_ALL && ~E_WARNING && ~E_NOTICE);
use PHPUnit\Framework\TestCase;

use R\Psr7\Stream;

final class StreamTest extends TestCase
{
    public function test_create()
    {
        $s = new Stream("test");
        $this->assertInstanceOf(Stream::class, $s);
    }

    public function test_toString()
    {
        $s = new Stream("test");
        $this->assertEquals("test", (string)$s);
    }

    public function test_getSize()
    {
        $s = new Stream("test");
        $this->assertEquals(4, $s->getSize());
    }

    public function test_write(){
        $s = new Stream("test");
        $s->write("abc");
        $this->assertEquals("testabc", (string)$s);

    }

}