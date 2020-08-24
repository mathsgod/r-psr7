<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once "vendor/autoload.php";

use PHP\Psr7\Message;
use PHP\Psr7\Stream;

$m1 = new Message();
$m1 = $m1->withBody(new Stream("test"));

$m2 = $m1->withHeader("m2", "a");

print_R((string)$m1->getBody());
