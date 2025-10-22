<?php

declare(strict_types=1);

namespace Doudenko\Api\Serializer;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class SerializerFactory
{
    public function createJsonSerializer(): SerializerInterface
    {
        return new Serializer(
            normalizers: [
                new ArrayDenormalizer(),
                new BackedEnumNormalizer(),
                new DateTimeNormalizer(),
                new ObjectNormalizer(
                    $this->createClassMetadataFactory(),
                    $this->createNameConverter(),
                ),
                new PropertyNormalizer(
                    $this->createClassMetadataFactory(),
                    $this->createNameConverter(),
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

    private function createClassMetadataFactory(): ClassMetadataFactoryInterface
    {
        return new ClassMetadataFactory(
            new AttributeLoader(),
        );
    }

    private function createNameConverter(): NameConverterInterface
    {
        return new MetadataAwareNameConverter(
            $this->createClassMetadataFactory(),
        );
    }
}
