<?
declare (strict_types = 1);
//error_reporting(E_ALL && ~E_WARNING);
use PHPUnit\Framework\TestCase;

use R\Psr7\ObjectStream;

final class ObjectStreamTest extends TestCase
{
    public function test_create()
    {
        $s = new ObjectStream();
        $this->assertInstanceOf(ObjectStream::class, $s);
    }

    public function testWrite()
    {
        $s = new ObjectStream();
        $s->write("abc");
        $s->write("def");
        $this->assertEquals("abcdef", (string)$s);
    }

}