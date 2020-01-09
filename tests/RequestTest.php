<?
declare (strict_types = 1);
//error_reporting(E_ALL && ~E_WARNING);
use PHPUnit\Framework\TestCase;


use R\Psr7\Request;
use R\Psr7\Uri;

final class RequestTest extends TestCase
{
    public function testCreate()
    {
        $r = new Request("GET", Uri::createFromString("http://raymond.hostlink.hk/test?a=1&b=2"));

        $this->assertInstanceOf(Request::class, $r);
    }
}