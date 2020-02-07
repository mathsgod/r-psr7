<?php

namespace R\Psr7;

use \Psr\Http\Message\ServerRequestInterface;

class ServerRequest extends Request implements ServerRequestInterface
{

    protected $attributes = [];
    protected $cookieParams = [];
    protected $parsedBody;
    protected $queryParams = [];
    protected $serverParams;
    protected $uploadedFiles = [];

    public function __construct($method, Uri $uri, array $headers = [], $body = null, $version = '1.1', $serverParams = [])
    {
        parse_str($uri->getQuery(), $this->queryParams);
        $this->serverParams = $serverParams;
        parent::__construct($method, $uri, $headers, $body, $version);
        foreach ($this->getHeader("Content-Type") as $value) {
            if (strstr($value, "application/json") == 0) {
                $this->parsedBody = json_decode(file_get_contents("php://input"), true);
                break;
            }
        }
    }

    public function getServerParams()
    {
        return $this->serverParams;
    }

    public function getCookieParams()
    {
        return $this->cookies;
    }

    public function getQueryParams()
    {
        parse_str($this->getUri()->getQuery(), $p);
        foreach ($this->queryParams as $k => $v) {
            $p[$k] = $v;
        }

        return $p;
    }

    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    public function withCookieParams(array $cookies)
    {
        $clone = clone $this;
        $clone = clone $this;
        $clone->cookies = $cookies;
        return $clone;
    }

    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        $uri = $clone->getUri()->withQuery(http_build_query($query));
        $clone->queryParams = array_merge($clone->queryParams, $query);
        return $clone->withUri($uri);
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;
        return $clone;
    }

    public function getParsedBody()
    {

        if (strpos($this->getHeader("Content-Type")[0], "application/x-www-form-urlencoded") !== false) {
            return $_POST;
        }

        if (strpos($this->getHeader("Content-Type")[0], "multipart/form-data") !== false) {
            return $_POST;
        }

        return $this->parsedBody;
    }

    public function withParsedBody($data)
    {
        $clone = clone $this;
        $clone->bodyParsed = $data;
        return $clone;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null)
    {
        return $this->attributes[$name] ? $this->attributes[$name] : $default;
    }

    public function withAttribute($name, $value)
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;
        return $clone;
    }

    public function withoutAttribute($name)
    {
        $clone = clone $this;
        $clone->attributes = [];
        return $clone;
    }

    public static function FromEnv()
    {
        $request = new ServerRequest(
            $_SERVER["REQUEST_METHOD"],
            Uri::createFromEnvironment($_SERVER),
            getallheaders(),
            new Stream(fopen("php://input", "r")),
            explode("/", $_SERVER["SERVER_PROTOCOL"])[1],
            $_SERVER
        );

        if ($_FILES) {
            $parseUploadedFile = function ($files) use (&$parseUploadedFile) {
                $data = [];
                foreach ($files as $name => $file) {
                    if (!isset($file["error"])) {
                        if (is_array($file)) {
                            $data[$name] = $parseUploadedFile($file);
                        }
                        continue;
                    }
                    $data[$name] = [];
                    if (!is_array($file["error"])) {
                        $data[$name] = new UploadedFile(new Stream(fopen($file["tmp_name"], "r")), $file["size"], $file["error"], $file["name"], $file["type"]);
                    } else {
                        $child = [];
                        foreach ($file['error'] as $id => $error) {
                            $child[$id]['name'] = $file['name'][$id];
                            $child[$id]['type'] = $file['type'][$id];
                            $child[$id]['tmp_name'] = $file['tmp_name'][$id];
                            $child[$id]['error'] = $file['error'][$id];
                            $child[$id]['size'] = $file['size'][$id];
                        }
                        $data[$name] = $parseUploadedFile($child);
                    }
                }
                return $data;
            };

            $request = $request->withUploadedFiles($parseUploadedFile($_FILES));
        }

        foreach ($request->getHeader("Content-Type") as $value) {
            if (strpos($value, "application/json") !== false) {
                $_POST = json_decode(file_get_contents("php://input"), true);
                break;
            }
        }

        return $request;
    }
}
