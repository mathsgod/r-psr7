<?

declare(strict_types=1);
error_reporting(E_ALL && ~E_WARNING && ~E_NOTICE);

use PHPUnit\Framework\TestCase;

use R\Psr7\ServerRequest;
use R\Psr7\Uri;

final class ServerRequestTest extends TestCase
{

    public function test_getallheaders()
    {
        $this->assertTrue(is_array(getallheaders()));
    }

    public function test_FromEnv()
    {
        $env = ServerRequest::FromEnv();
        $this->assertInstanceOf(ServerRequest::class, $env);
    }

    public function test_create()
    {
        $r = new ServerRequest("GET", Uri::CreateFromString("https://raymond.hostlink.com.hk/test"));
        $this->assertInstanceOf(ServerRequest::class, $r);
    }
}
