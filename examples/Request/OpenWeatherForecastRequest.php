<?php

declare(strict_types=1);

namespace Doudenko\Api\Examples\Request;

use Doudenko\Api\Client\HttpMethod;
use Doudenko\Api\Request\AbstractApiRequest;

final class OpenWeatherForecastRequest extends AbstractApiRequest
{
    public HttpMethod $httpMethod = HttpMethod::Get;
    public string $uri = '/v1/forecast';

    public function getPayload(): array
    {
        return [
            'timezone' => 'Europe/Moscow',
            'latitude' => 55.8546805,
            'longitude' => 37.615522217,
            'current' => implode(',', [
                'temperature_2m',
                'apparent_temperature',
                'wind_speed_10m',
                'wind_direction_10m',
                'precipitation',
                'precipitation_probability',
                'rain',
                'snowfall',
            ]),
            'forecast_days' => 1,
            'wind_speed_unit' => 'ms',
        ];
    }
}
