<?

use R\Psr7\Stream;

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

ini_set("display_errors", "On");
require_once("../vendor/autoload.php");
$request = R\Psr7\ServerRequest::FromEnv();

$s = popen("dir", "r");

/*$s=fopen("php://memory","a+");
print_r(stream_get_meta_data($s));

echo "a";
die();

print_r(stream_get_contents($s));

print_r(stream_get_meta_data($s));

rewind($s);

die();*/
$p = new Stream($s);

echo (string) $p;
echo (string) $p;
die();

print_r(stream_get_meta_data($handle));
die();
$uri = $request->getUri();
print_r($uri);
die();

$uri = R\Psr7\Uri::CreateFromString("http://username:password1@testing.hostlink.com.hk:456/User/1/v?a=1#/hash/1/v");

print_r($uri);


$uri = R\Psr7\Uri::CreateFromString("http://a.com/");

print_r($uri);
