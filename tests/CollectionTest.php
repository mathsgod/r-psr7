<?
declare (strict_types = 1);
//error_reporting(E_ALL && ~E_WARNING);
use PHPUnit\Framework\TestCase;


use R\Psr7\Collection;

final class CollectionTest extends TestCase
{
    public function testCreate()
    {
        $r = new Collection();

        $this->assertInstanceOf(Collection::class, $r);
    }

    public function testAdd()
    {
        $c = new Collection();
        $c->add("a",1);

        $this->assertTrue($c->has("a"));

    }
}