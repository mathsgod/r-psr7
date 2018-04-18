<?php

namespace R\Psr7;

use \Psr\Http\Message\ResponseInterface;

class Response extends Message implements ResponseInterface
{
    protected $status = 200;
    protected $reasonPhrase = '';

    static $MESSAGES = [
        //Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        //Successful 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        //Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        //Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        //Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];

    public function __construct($status, $headers, $body = null, $version = '1.1')
    {
        $this->status=$status;
        if ($body===null) {
            $body=new Stream(fopen("php://memory", "r+"));
        }
        parent::__construct($headers, $body, $version);
    }

    public function getStatusCode()
    {
        return $this->status;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        //$code = $this->filterStatus($code);
        if (!is_string($reasonPhrase) && !method_exists($reasonPhrase, '__toString')) {
            throw new InvalidArgumentException('ReasonPhrase must be a string');
        }
        $clone = clone $this;
        $clone->status = $code;
        if ($reasonPhrase === '' && isset(static::$MESSAGES[$code])) {
            $reasonPhrase = static::$MESSAGES[$code];
        }
        if ($reasonPhrase === '') {
            throw new InvalidArgumentException('ReasonPhrase must be supplied for this code');
        }
        $clone->reasonPhrase = $reasonPhrase;
        return $clone;
    }

    public function getReasonPhrase()
    {
        if ($this->reasonPhrase) {
            return $this->reasonPhrase;
        }
        if (isset(static::$MESSAGES[$this->status])) {
            return static::$MESSAGES[$this->status];
        }
        return '';
    }
}
