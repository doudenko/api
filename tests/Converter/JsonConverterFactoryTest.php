<?php

declare(strict_types=1);

namespace Doudenko\Api\Tests\Converter;

use Doudenko\Api\Converter\JsonConverterFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class JsonConverterFactoryTest extends TestCase
{
    #[Test]
    public function createPositive(): void
    {
        $converterFactory = new JsonConverterFactory();

        $actualConverter = $converterFactory->create();

        self::assertInstanceOf(SerializerInterface::class, $actualConverter);
        self::assertInstanceOf(NormalizerInterface::class, $actualConverter);
    }
}
