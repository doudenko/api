<?php

declare(strict_types=1);

namespace Doudenko\Api\Examples\Response;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class OpenWeatherForecastResponsePayload
{
    #[SerializedName('temperature_2m')]
    public float $temperature;
    public float $apparentTemperature;
    #[SerializedName('wind_speed_10m')]
    public float $windSpeed;
    #[SerializedName('wind_direction_10m')]
    public int $windDirection;
    public float $precipitation;
    public float $precipitationProbability;
    public float $rain;
    public float $snowfall;
}
