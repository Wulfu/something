<?php
/**
 * Created by PhpStorm.
 * User: mateusz
 * Date: 26.01.19
 * Time: 22:37
 */

namespace App\Service\OpenWeatherApiClient;


use App\Service\OpenWeatherApiClient\Exception\FailedApiCallException;
use App\Service\OpenWeatherApiClient\Exception\InvalidLangValueException;
use App\Service\OpenWeatherApiClient\Exception\InvalidUnitsFormatException;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Class OpenWeatherApiClient
 * @package App\Service\OpenWeatherApiClient
 * @property Container $container
 * @property FilesystemCache $cache
 */
class OpenWeatherApiClient implements OpenWeatherApiClientInterface
{
    public $container;
    public $cache;

    public function __construct(ContainerInterface $container, CacheInterface $cache)
    {
        $this->container = $container;
        $this->cache = $cache;
    }

    /**
     * @param string $city
     * @param string|null $countryCode
     * @param string $units
     * @param bool $arrayAssoc
     * @return bool|string
     * @throws FailedApiCallException
     * @throws InvalidArgumentException
     * @throws InvalidUnitsFormatException
     * @throws InvalidLangValueException
     */
    public function currentCityTemp(string $city, ?string $countryCode = null, $units = null, $arrayAssoc = false)
    {
        if($this->cache->has($this->buildCacheKey($city, $countryCode))){
            $data = $this->cache->get($this->buildCacheKey($city, $countryCode));

            if($arrayAssoc){
                return json_decode($data, true);
            }

            return $data;
        }

        if(is_null($units)) {
            $units = $this->container->getParameter('open_weather_default_units');
        }

        $uri = "weather?q={$city},{$countryCode}";

        $url = $this->makeFullUrl($uri, $units);

        $result = $this->sendRequest($url);
        if($result){
            $this->cache->setMultiple([
                $this->buildCacheKey($city, $countryCode) =>  $result,
                $this->buildCacheKey($city,$countryCode) . ".created_at" => time()
            ]);

            $arrayAssoc ? $data = json_decode($result, true) : $data = $result;

            return $data;
        } else {
            throw new FailedApiCallException();
        }
    }

    /**
     * @param string $city
     * @param string $countryCode
     * @return string
     */
    public function buildCacheKey(string $city, string $countryCode): string
    {
        $cacheMainKey = OpenWeatherApiEnum::CACHE_KEY;
        return "{$cacheMainKey}.$countryCode.$city";
    }

    /**
     * @param $city
     * @param $countryCode
     * @return \DateTime
     * @throws InvalidArgumentException
     */
    public function lastCacheUpdate($city, $countryCode)
    {
        $time = $this->cache->get($this->buildCacheKey($city, $countryCode) . ".created_at");
        $date = new \DateTime();
        return $date->setTimestamp($time);
    }

    /**
     * @param string $url
     * @return bool|string
     */
    protected function sendRequest(string $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * @param string $uri
     * @param string $units
     * @return string|null
     * @throws InvalidUnitsFormatException
     * @throws InvalidLangValueException
     */
    protected function makeFullUrl(string $uri, string $units): ?string
    {
        if(!in_array($units, OpenWeatherApiEnum::UNITS_FORMATS)) {
            throw new InvalidUnitsFormatException();
        }
        $lang = $this->container->getParameter('locale');
        if(!in_array($lang, OpenWeatherApiEnum::LANG_VALUES)) {
            throw new InvalidLangValueException();
        }
        $langParamString = "&lang=$lang";
        $unitsParamString = "&units=$units";

        $baseUrl = $this->container->getParameter('open_weather_base_url');
        $apiVersion = OpenWeatherApiEnum::API_VERSION;
        $apiKey = $this->container->getParameter('open_weather_api_key');
        $fullUrl = "{$baseUrl}/data/{$apiVersion}/{$uri}&APPID={$apiKey}{$unitsParamString}{$langParamString}";

        return $fullUrl;
    }
}