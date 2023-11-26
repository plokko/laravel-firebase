<?php
namespace Plokko\LaravelFirebase\Exceptions;

use Exception;
use Throwable;

class FcmTargetNotSpecifiedException extends Exception
{
    function __construct(string $message = "FCM message target not specified!", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
