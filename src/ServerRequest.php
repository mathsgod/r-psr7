<?php

namespace R\Psr7;

use \Psr\Http\Message\ServerRequestInterface;
use \PHP\Psr7\ServerRequestTrait;

/**
 * @method Uri getUri
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    use ServerRequestTrait;

    public static function FromEnv()
    {
        $request = new self();

        foreach ($request->getHeader("Content-Type") as $value) {
            if (strpos($value, "application/json") !== false) {
                $_POST = json_decode(file_get_contents("php://input"), true);
                break;
            }
        }

        $uri = Uri::CreateFromEnvironment($_SERVER);
        $request = $request->withUri($uri);

        return $request;
    }
}
