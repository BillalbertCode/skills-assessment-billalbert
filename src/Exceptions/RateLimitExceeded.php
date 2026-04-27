<?php

namespace Dustov\Quotes\Exceptions;

use Exception;

class RateLimitExceeded extends Exception
{
    protected $message = 'Rate limit exceeded.';
    protected $code = 429;
}
