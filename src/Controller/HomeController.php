<?php
/**
 * Created by PhpStorm.
 * User: mateusz
 * Date: 26.01.19
 * Time: 14:35
 */

namespace App\Controller;

use App\Service\OpenWeatherApiClient\OpenWeatherApiClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AppController
{
    /**
     * @Route("/", name="home_page")
     * @param OpenWeatherApiClient $openWeatherApiClient
     * @return Response
     * @throws \App\Service\OpenWeatherApiClient\Exception\FailedApiCallException
     * @throws \App\Service\OpenWeatherApiClient\Exception\InvalidUnitsFormatException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \App\Service\OpenWeatherApiClient\Exception\InvalidLangValueException
     */
    public function index(OpenWeatherApiClient $openWeatherApiClient)
    {
        $weatherData = $openWeatherApiClient->currentCityTemp('Warsaw', 'pl', null, true);
        $lastWeatherUpdate = $openWeatherApiClient->lastCacheUpdate('Warsaw', 'pl');

        return $this->render('homepage/index.html.twig', [
            'weather_data' => $weatherData,
            'last_weather_update' => $lastWeatherUpdate
        ]);
    }
}