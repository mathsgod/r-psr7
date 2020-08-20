<?php

namespace R\Psr7;

use PHP\Psr7\Uri as Psr7Uri;

class Uri extends Psr7Uri
{
    protected $basePath = '';

    public static function CreateFromString(string $uri)
    {
        return new self($uri);
    }

    public function withBasePath(string $basePath)
    {
        $clone = clone $this;
        $clone->basePath = $basePath;
        return $clone;
    }

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function __toString()
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $basePath = $this->getBasePath();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();
        if ($path[0] != "/") {
            $path = "/" . $path;
        }
        $path = $basePath . $path;

        return ($scheme ? $scheme . ':' : '')
            . ($authority ? '//' . $authority : '')
            . $path
            . ($query ? '?' . $query : '')
            . ($fragment ? '#' . $fragment : '');
    }

    public static function CreateFromEnvironment(array $env)
    {
        $uri = new Uri();
        if ($scheme = $env["REQUEST_SCHEME"]) {
            $uri = $uri->withScheme($scheme);
        }

        if (isset($env['SERVER_NAME'])) {
            $uri = $uri->withHost($env['SERVER_NAME']);
        } elseif (isset($env['HTTP_HOST'])) {
            $uri = $uri->withHost($env['HTTP_HOST']);
        }


        if ($env["PHP_AUTH_USER"]) {
            $uri = $uri->withUserInfo($env["PHP_AUTH_USER"], $env["PHP_AUTH_PW"]);
        }

        $basePath = dirname($env["SCRIPT_NAME"]);
        if ($basePath == DIRECTORY_SEPARATOR) {
            $basePath = "";
        }
        $uri = $uri->withBasePath($basePath);


        if ($path = parse_url($env["REQUEST_URI"], PHP_URL_PATH)) {
            $path = substr($path, strlen($basePath));
            $uri = $uri->withPath($path);
        }

        $uri = $uri->withQuery($env["QUERY_STRING"] ?? "");


        if ($port = $env["SERVER_PORT"]) {
            $uri = $uri->withPort(intval($port));
        }
        return $uri;
    }
}
