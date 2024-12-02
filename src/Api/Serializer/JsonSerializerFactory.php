<?php

declare(strict_types=1);

namespace Doudenko\Api\Serializer;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class JsonSerializerFactory
{
    public function create(): SerializerInterface
    {
        return new Serializer(
            normalizers: [
                new ArrayDenormalizer(),
                new DateTimeNormalizer(),
                new PropertyNormalizer(
                    $classMetadataFactory = new ClassMetadataFactory(
                        new AttributeLoader(),
                    ),
                    new MetadataAwareNameConverter($classMetadataFactory),
                    new PropertyInfoExtractor(
                        typeExtractors: [
                            new ReflectionExtractor(),
                            new PhpDocExtractor(),
                        ],
                    ),
                ),
            ],
            encoders: [
                new JsonEncoder(),
            ],
        );
    }
}
