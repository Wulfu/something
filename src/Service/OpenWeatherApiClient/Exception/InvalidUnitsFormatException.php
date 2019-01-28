<?php
/**
 * Created by PhpStorm.
 * User: mateusz
 * Date: 27.01.19
 * Time: 14:14
 */

namespace App\Service\OpenWeatherApiClient\Exception;

use Throwable;

class InvalidUnitsFormatException extends AbstractException implements NotCorrectEnumValueExceptionInterface
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}