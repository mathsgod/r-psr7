<?php

namespace R\Psr7;

use \InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    protected $scheme = '';
    protected $user = '';
    protected $password = '';
    protected $port;
    protected $basePath = '';
    protected $path = '';
    protected $query = '';
    protected $fragment = '';

    public function __construct(
        string $scheme,
        $host,
        $port = null,
        $path = '',
        $query = '',
        $fragment = '',
        $user,
        $password
    ) {
        $this->scheme = $this->filterScheme($scheme);
        $this->host = $host;
        $this->port = $this->filterPort($port);
        $this->path = $this->filterPath($path);
        $this->query = $this->filterQuery($query);
        $this->fragment = $this->filterQuery($fragment);
        $this->user = $user;
        $this->password = $password;
    }

    public static function CreateFromString(string $uri): self
    {
        $parts = parse_url($uri);
        $scheme = $parts['scheme'] ?? null;
        $user = $parts['user'] ?? null;
        $pass = $parts['pass'] ?? null;
        $host = $parts['host'] ?? '';
        $port = $parts['port'] ?? null;
        $path = $parts['path'] ?? '';
        $query = $parts['query'] ?? '';
        $fragment = $parts['fragment'] ?? '';

        return new static($scheme, $host, $port, $path, $query, $fragment, $user, $pass);
    }

    public static function CreateFromEnvironment(array $env)
    {
        // Scheme
        $isSecure = isset($env["HTTPS"]) ? $env["HTTPS"] : null;
        $scheme = (empty($isSecure) || $isSecure === 'off') ? 'http' : 'https';
        // Authority: Username and password
        $username = $env['PHP_AUTH_USER'];
        $password = $env['PHP_AUTH_PW'];
        // Authority: Host
        if ($env['HTTP_HOST']) {
            $host = $env['HTTP_HOST'];
        } else {
            $host = $env['SERVER_NAME'];
        }
        // Authority: Port
        $port = (int) $env['SERVER_PORT'];
        if (preg_match('/^(\[[a-fA-F0-9:.]+\])(:\d+)?\z/', $host, $matches)) {
            $host = $matches[1];
            if (isset($matches[2])) {
                $port = (int) substr($matches[2], 1);
            }
        } else {
            $pos = strpos($host, ':');
            if ($pos !== false) {
                $port = (int) substr($host, $pos + 1);
                $host = strstr($host, ':', true);
            }
        }
        if ($port == 0) {
            $port = null;
        }

        $basePath = dirname($env["SCRIPT_NAME"]);
        if ($basePath == DIRECTORY_SEPARATOR) {
            $basePath = "";
        }

        $requestUri = parse_url('http://example.com' . $env['REQUEST_URI'], PHP_URL_PATH);

        $virtualPath = substr($requestUri, strlen($basePath));

        // Query string
        $queryString = $env['QUERY_STRING'] ?? '';
        if ($queryString === '') {
            $queryString = parse_url('http://example.com' . $env['REQUEST_URI'], PHP_URL_QUERY);
        }
        // Fragment
        $fragment = '';
        // Build Uri
        $uri = new static($scheme, $host, $port, $virtualPath, $queryString, $fragment, $username, $password);
        $uri = $uri->withBasePath($basePath);
        return $uri;
    }

    public function withBasePath(string $basePath): self
    {
        $clone = clone $this;
        $clone->basePath = $basePath;
        return $clone;
    }

    protected function filterQuery($query):string 
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $query
        );
    }
    protected function filterPath($path)
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $path
        );
    }


    protected function filterPort($port)
    {
        if (is_null($port) || (is_integer($port) && ($port >= 1 && $port <= 65535))) {
            return $port;
        }
        throw new InvalidArgumentException('Uri port must be null or an integer between 1 and 65535 (inclusive)');
    }


    protected function filterScheme(string $scheme): string
    {
        static $valid = [
            '' => true,
            'https' => true,
            'http' => true,
        ];
        $scheme = str_replace('://', '', strtolower((string) $scheme));
        if (!isset($valid[$scheme])) {
            throw new InvalidArgumentException('Uri scheme must be one of: "", "https", "http"');
        }
        return $scheme;
    }

    public function withScheme($scheme)
    {
        $scheme = $this->filterScheme($scheme);
        $clone = clone $this;
        $clone->scheme = $scheme;
        return $clone;
    }
    public function getAuthority(): string
    {
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $port = $this->getPort();
        if (($this->port == 80 && $this->scheme == 'http') || ($this->port == 443 && $this->scheme == 'https')) {
            $port = null;
        }

        return ($userInfo ? $userInfo . '@' : '') . $host . ($port !== null ? ':' . $port : '');
    }
    public function getUserInfo(): string
    {
        return $this->user . ($this->password ? ':' . $this->password : '');
    }

    public function withUserInfo($user, $password = null): self
    {
        $clone = clone $this;
        $clone->user = $user;
        $clone->password = $password;
        return $clone;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function withHost($host): self
    {
        $clone = clone $this;
        $clone->host = $host;
        return $clone;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withPort($port): self
    {
        $port = $this->filterPort($port);
        $clone = clone $this;
        $clone->port = $port;
        return $clone;
    }

    public function withPath($path): self
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException('Uri path must be a string');
        }
        $clone = clone $this;
        // if the path is absolute
        if (substr($path, 0, 1) == '/') {
            if (substr($path, 0, strlen($this->basePath)) == $this->basePath) {
                $path = substr($path, strlen($this->basePath));
            } else {
                $clone->basePath = '/';
                $path = substr($path, 1);
            }
        }

        $clone->path = $this->filterPath($path);
        return $clone;
    }
    public function withQuery($query): self
    {
        if (!is_string($query) && !method_exists($query, '__toString')) {
            throw new InvalidArgumentException('Uri query must be a string');
        }
        $query = ltrim((string) $query, '?');
        $clone = clone $this;
        $clone->query = $this->filterQuery($query);
        return $clone;
    }

    public function withFragment($fragment): self
    {
        if (!is_string($fragment) && !method_exists($fragment, '__toString')) {
            throw new InvalidArgumentException('Uri fragment must be a string');
        }
        $fragment = ltrim((string) $fragment, '#');
        $clone = clone $this;
        $clone->fragment = $this->filterQuery($fragment);
        return $clone;
    }

    public function __toString()
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $basePath = $this->getBasePath();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();
        $path = $basePath . $path;

        return ($scheme ? $scheme . ':' : '')
            . ($authority ? '//' . $authority : '')
            . $path
            . ($query ? '?' . $query : '')
            . ($fragment ? '#' . $fragment : '');
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }
}
