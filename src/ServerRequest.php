<?php

namespace R\Psr7;

use PHP\Psr7\ServerRequest as Psr7ServerRequest;
use \Psr\Http\Message\ServerRequestInterface;

class ServerRequest extends Psr7ServerRequest
{

    public static function FromEnv()
    {
        $request = new self();

        foreach ($request->getHeader("Content-Type") as $value) {
            if (strpos($value, "application/json") !== false) {
                $_POST = json_decode(file_get_contents("php://input"), true);
                break;
            }
        }

        return $request;
    }
}
