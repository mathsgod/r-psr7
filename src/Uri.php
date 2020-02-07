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

    const DEFAULT_PORTS = [
        'http'  => 80,
        'https' => 443,
        'ftp' => 21,
        'gopher' => 70,
        'nntp' => 119,
        'news' => 119,
        'telnet' => 23,
        'tn3270' => 23,
        'imap' => 143,
        'pop' => 110,
        'ldap' => 389,
    ];

    public function __construct(string $uri = '')
    {
        if ($uri !== '') {
            $parts = parse_url($uri);

            $this->scheme = $parts["scheme"] ? $this->filterScheme($parts["scheme"]) : '';
            $this->host = $parts["host"] ? $this->filterHost($parts["host"]) : '';
            $this->port = $parts["port"] ? $this->filterPort($parts["port"]) : null;
            $this->path = $this->filterPath($parts["path"]);
            $this->query = $this->filterQuery($parts["query"]);
            $this->fragment = $this->filterQuery($parts["fragment"]);
            $this->user = $parts["user"];
            $this->password = $parts["pass"];
        }
    }

    protected function filterHost(string $host)
    {
        return strtolower($host);
    }

    /**
     * @deprecated 
     */
    public static function CreateFromString(string $uri): self
    {
        return new self($uri);
    }

    public static function CreateFromEnvironment(array $env)
    {
        $url = "";
        // Scheme
        $isSecure = isset($env["HTTPS"]) ? $env["HTTPS"] : null;
        $scheme = (empty($isSecure) || $isSecure === 'off') ? 'http' : 'https';
        // Authority: Username and password
        $username = $env['PHP_AUTH_USER'];
        $password = $env['PHP_AUTH_PW'];

        $url = $scheme . "://";

        // Authority: Host
        if ($env['HTTP_HOST']) {
            $host = $env['HTTP_HOST'];
        } else {
            $host = $env['SERVER_NAME'];
        }

        $url .= $host;

        // Authority: Port
        $port = (int) $env['SERVER_PORT'];
        $url .= ":$port";

        $basePath = dirname($env["SCRIPT_NAME"]);
        if ($basePath == DIRECTORY_SEPARATOR) {
            $basePath = "";
        }

        $url .= $env["REQUEST_URI"];

        $uri = new static($url);
        $uri = $uri->withBasePath($basePath);
        $uri = $uri->withPath($uri->getPath());
        return $uri;
    }

    public function withBasePath(string $basePath): self
    {
        $clone = clone $this;
        $clone->basePath = $basePath;
        return $clone;
    }

    protected function filterQuery($query): string
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
        $scheme = str_replace('://', '', strtolower((string) $scheme));
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
        $clone->host = $this->filterHost($host);
        return $clone;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getPort()
    {
        if ($this->port) {
            return ($this->port == self::DEFAULT_PORTS[$this->getScheme()]) ? null : $this->port;
        }
        return null;
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
        $clone = clone $this;
        $clone->port = $this->filterPort($port);
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
        } else {
            $path = "/" . $path;
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
