<?php

namespace R\Psr7;

use PHP\Psr7\Request as Psr7Request;

class Request extends Psr7Request implements RequestInterface
{
    use RequestTrait;
}
