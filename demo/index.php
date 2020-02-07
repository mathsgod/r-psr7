<?

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

ini_set("display_errors", "On");
require_once("../vendor/autoload.php");
$request = R\Psr7\ServerRequest::FromEnv();
$uri=$request->getUri();
print_r($uri);
die();

$uri = R\Psr7\Uri::CreateFromString("http://username:password1@testing.hostlink.com.hk:456/User/1/v?a=1#/hash/1/v");

print_r($uri);


$uri = R\Psr7\Uri::CreateFromString("http://a.com/");

print_r($uri);
