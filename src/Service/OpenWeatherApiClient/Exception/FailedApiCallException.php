<?php
/**
 * Created by PhpStorm.
 * User: mateusz
 * Date: 27.01.19
 * Time: 17:41
 */

namespace App\Service\OpenWeatherApiClient\Exception;

use Throwable;

class FailedApiCallException extends AbstractException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}