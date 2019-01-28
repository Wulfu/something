<?php
/**
 * Created by PhpStorm.
 * User: mateusz
 * Date: 27.01.19
 * Time: 12:53
 */

namespace App\Service\OpenWeatherApiClient;


class OpenWeatherApiEnum
{
    const API_VERSION = '2.5';

    const UNITS_FORMATS = [
        'metric',
        'imperial'
    ];

    const CACHE_KEY = 'open_weather';

    const LANG_VALUES = [
        'pl', 'en'
    ];
}