<?php
require_once "vendor/autoload.php";

use R\Psr7\Message;

$r = new Message(["a" => "1", "b" => "2"]);
print_R($r->getHeaders());
