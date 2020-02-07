<?

declare(strict_types=1);
//error_reporting(E_ALL && ~E_WARNING);

use PHPUnit\Framework\TestCase;

use R\Psr7\Uri;

final class UriTest extends TestCase
{
    public function test_getAuthority()
    {
        $uri = Uri::createFromString("http://a:b@raymond2.hostlink.com.hk:8080/cms/testing/download");
        $this->assertEquals("a:b@raymond2.hostlink.com.hk:8080", $uri->getAuthority());

        $uri = Uri::createFromString("http://a:b@raymond2.hostlink.com.hk:80/cms/testing/download");
        $this->assertEquals("a:b@raymond2.hostlink.com.hk", $uri->getAuthority());
    }

    public function test_getScheme()
    {

        $uri = Uri::createFromString("http://raymond2.hostlink.com.hk/cms/testing/download");
        $this->assertEquals("http", $uri->getScheme());
    }

    public function test_getPort()
    {

        $uri = Uri::createFromString("http://raymond2.hostlink.com.hk/cms/testing/download");
        $this->assertNull($uri->getPort());

        $uri = Uri::createFromString("http://raymond2.hostlink.com.hk:80/cms/testing/download");
        $this->assertNull($uri->getPort());

        $uri = Uri::createFromString("raymond2.hostlink.com.hk/cms/testing/download");
        $this->assertNull($uri->getPort());

        $uri = Uri::createFromString("ftp://raymond2.hostlink.com.hk/cms/testing/download");
        $this->assertNull($uri->getPort());
    }


    public function test_withPath()
    {
        $uri = Uri::createFromString("http://raymond2.hostlink.com.hk/cms/testing/download");
        $uri = $uri->withBasePath("/cms");
        $uri = $uri->withPath("abc/def");

        $this->assertEquals("http://raymond2.hostlink.com.hk/cms/abc/def", (string) $uri);
    }

    public function test_port()
    {
        $uri = Uri::createFromEnvironment($_SERVER);
        $this->assertNull($uri->getPort());
    }

    public function testCreateFromString()
    {
        $uri = Uri::createFromString("http://raymond2.hostlink.com.hk/testing/download?a=1&b=2&c=3#hash/x/1");
        $this->assertInstanceOf(Uri::class, $uri);


        $this->assertEquals("raymond2.hostlink.com.hk", $uri->getHost());
        $this->assertEquals("/testing/download", $uri->getPath());
        $this->assertEquals("a=1&b=2&c=3", $uri->getQuery());
        $this->assertEquals("hash/x/1", $uri->getFragment());
    }

    public function test_basePath()
    {
        $uri = Uri::createFromString("http://raymond2.hostlink.com.hk/testing/download?a=1&b=2&c=3#hash/x/1");
        $this->assertEquals("", $uri->getBasePath());

        $uri = $uri->withBasePath('/base_path');
        $this->assertEquals("http://raymond2.hostlink.com.hk/base_path/testing/download?a=1&b=2&c=3#hash/x/1", (string) $uri);

        $this->assertEquals("/base_path", $uri->getBasePath());
    }

    public function test_host()
    {
        $uri = Uri::createFromString("http://raymond2.hostlink.com.hk/testing/download?a=1&b=2&c=3#hash/x/1");
        $this->assertEquals("raymond2.hostlink.com.hk", $uri->getHost());

        $uri = $uri->withHost('raymond.hostlink.com.hk');
        $this->assertEquals("raymond.hostlink.com.hk", $uri->getHost());
    }

    public function test_path()
    {
        $uri = Uri::createFromString("http://raymond2.hostlink.com.hk/testing/download?a=1&b=2&c=3#hash/x/1");
        $this->assertEquals("/testing/download", $uri->getPath());

        $uri = $uri->withPath("/testing2/abc");
        $this->assertEquals("/testing2/abc", $uri->getPath());
        $this->assertEquals("http://raymond2.hostlink.com.hk/testing2/abc?a=1&b=2&c=3#hash/x/1", (string) $uri);
    }

    public function test_query()
    {
        $uri = Uri::createFromString("http://raymond2.hostlink.com.hk/testing/download?a=1&b=2&c=3#hash/x/1");
        $this->assertEquals("a=1&b=2&c=3", $uri->getQuery());

        $uri = $uri->withQuery("x=4&y=5&z=6");
        $this->assertEquals("x=4&y=5&z=6", $uri->getQuery());

        $this->assertEquals("http://raymond2.hostlink.com.hk/testing/download?x=4&y=5&z=6#hash/x/1", (string) $uri);
    }

    public function test_fragment()
    {
        $uri = Uri::createFromString("http://raymond2.hostlink.com.hk/testing/download?a=1&b=2&c=3#hash/x/1");
        $this->assertEquals("hash/x/1", $uri->getFragment());

        $uri = $uri->withFragment("hello/a/2");
        $this->assertEquals("hello/a/2", $uri->getFragment());
        $this->assertEquals("http://raymond2.hostlink.com.hk/testing/download?a=1&b=2&c=3#hello/a/2", (string) $uri);
    }

    public function test_createFromEnvironment()
    {

        //window
        $env = [];
        $env["HTTP_HOST"] = "127.0.0.1";
        $env["SERVER_PORT"] = 80;
        $env["DOCUMENT_ROOT"] = "C:/Users/maths/Desktop/web";
        $env["SCRIPT_FILENAME"] = "C:/Users/maths/Desktop/web/cms/index.php";
        $env["REQUEST_URI"] = "/cms/Testing/a";
        $env["SCRIPT_NAME"] = "/cms/index.php";

        $uri = Uri::createFromEnvironment($env);

        $this->assertEquals("/cms", $uri->getBasePath());
        $this->assertEquals("/Testing/a", $uri->getPath());
        $this->assertEquals("127.0.0.1", $uri->getHost());


        //window
        $env = [];
        $env["HTTP_HOST"] = "127.0.0.1";
        $env["SERVER_PORT"] = 80;
        $env["DOCUMENT_ROOT"] = "C:/Users/maths/Desktop/web";
        $env["SCRIPT_FILENAME"] = "C:/Users/maths/Desktop/web/f/index.php";
        $env["REQUEST_URI"] = "/f/testbase";
        $env["SCRIPT_NAME"] = "/f/index.php";

        $uri = Uri::createFromEnvironment($env);

        $this->assertEquals("/f", $uri->getBasePath());
        $this->assertEquals("/testbase", $uri->getPath());
        $this->assertEquals("127.0.0.1", $uri->getHost());

        //linux
        $env = [];
        $env["HTTP_HOST"] = "raymond2.hostlink.com.hk";
        $env["SERVER_PORT"] = 80;
        $env["DOCUMENT_ROOT"] = "/home/vhosts/raymond2/public_html";
        $env["SCRIPT_FILENAME"] = "/home/vhosts/raymond2/public_html/index.php";
        $env["REQUEST_URI"] = "/v1";
        $env["SCRIPT_NAME"] = "/index.php";
        $uri = Uri::createFromEnvironment($env);
        $this->assertEquals("", $uri->getBasePath());
        $this->assertEquals("/v1", $uri->getPath());
        $this->assertEquals("raymond2.hostlink.com.hk", $uri->getHost());


        //linux
        $env = [];
        $env["HTTP_HOST"] = "raymond2.hostlink.com.hk";
        $env["SERVER_PORT"] = 80;
        $env["DOCUMENT_ROOT"] = "/home/vhosts/raymond2/public_html";
        $env["SCRIPT_FILENAME"] = "/home/vhosts/raymond2/public_html/index.php";
        $env["REQUEST_URI"] = "/v1/index/abc/def?a=1&b=2";
        $env["QUERY_STRING"] = "a=1&b=2";
        $env["SCRIPT_NAME"] = "/index.php";
        $uri = Uri::createFromEnvironment($env);
        $this->assertEquals("", $uri->getBasePath());
        $this->assertEquals("/v1/index/abc/def", $uri->getPath());
        $this->assertEquals("raymond2.hostlink.com.hk", $uri->getHost());
        $this->assertEquals("a=1&b=2", $uri->getQuery());
    }
}
