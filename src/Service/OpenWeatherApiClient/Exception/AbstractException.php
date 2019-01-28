<?php
/**
 * Created by PhpStorm.
 * User: mateusz
 * Date: 27.01.19
 * Time: 14:18
 */

namespace App\Service\OpenWeatherApiClient\Exception;

use Throwable;

class AbstractException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $message = "INVALID UNIT FORMAT PROVIDED";
        parent::__construct($message, $code, $previous);
    }
}