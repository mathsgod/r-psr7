<?

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use R\Psr7\ObjectStream;

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
}
