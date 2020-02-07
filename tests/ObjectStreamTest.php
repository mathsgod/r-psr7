<?

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use R\Psr7\ObjectStream;
use R\Psr7\Stream;

final class ObjectStreamTest extends TestCase
{
    public function test_isWritable()
    {
        $s = new ObjectStream();
        $this->assertTrue($s->isWritable());
    }

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
        $this->assertEquals("abcdef", (string) $s);
    }

    public function testWrite2()
    {
        $s = new ObjectStream();
        $str1 = new Stream("abc");


        $s->write($str1);
        $s->write("def");

        $str1->truncate(0);
        $str1->write("123");

        $this->assertEquals("123def", (string) $s);
    }
}
