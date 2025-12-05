<?php

declare(strict_types=1);

use Doudenko\Api\Client\ApiClient;
use Doudenko\Api\Client\ApiConfiguration;
use Doudenko\Api\Client\HttpOptions;
use Doudenko\Api\Client\HttpRequestFactory;
use Doudenko\Api\Converter\JsonConverterFactory;
use Doudenko\Api\Examples\Request\OpenWeatherForecastRequest;
use Doudenko\Api\Examples\Response\OpenWeatherForecastResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$client = new ApiClient(
    new HttpRequestFactory(
        new HttpFactory(),
        $converter = new JsonConverterFactory()->create(),
    ),
    new Client(),
    $converter,
    $configuration = new ApiConfiguration(
        baseUri: 'https://api.open-meteo.com',
        httpOptions: new HttpOptions(
            timeout: 30,
            connectionTimeout: 5,
        ),
    ),
);

$response = $client->send(
    $request = new OpenWeatherForecastRequest(),
    OpenWeatherForecastResponse::class,
);

print_r($response);
