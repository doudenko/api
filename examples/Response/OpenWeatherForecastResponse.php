<?php

declare(strict_types=1);

namespace Doudenko\Api\Examples\Response;

use Doudenko\Api\Response\ApiResponse;

final readonly class OpenWeatherForecastResponse extends ApiResponse
{
    public string $timezone;
    public float $latitude;
    public float $longitude;
    public OpenWeatherForecastResponsePayload $current;
}
