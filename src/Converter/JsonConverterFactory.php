<?php

declare(strict_types=1);

namespace Doudenko\Api\Converter;

use Symfony\Component\PropertyInfo\Extractor\ConstructorExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Mapping\Loader\LoaderChain;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_UNESCAPED_UNICODE;

final readonly class JsonConverterFactory implements ConverterFactoryInterface
{
    /**
     * @param array<string, mixed> $defaultContext
     */
    public function __construct(
        private array $defaultContext = [
            JsonEncode::OPTIONS => JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION,
        ],
    ) {
    }

    public function create(): SerializerInterface & NormalizerInterface
    {
        return new Serializer(
            normalizers: [
                new ArrayDenormalizer(),
                new BackedEnumNormalizer(),
                new DateTimeNormalizer(),
                new PropertyNormalizer(
                    $this->createClassMetadataFactory(),
                    $this->createNameConverter(),
                    new PropertyInfoExtractor(
                        typeExtractors: [
                            new ConstructorExtractor(),
                            new ReflectionExtractor(),
                            new PhpDocExtractor(),
                        ],
                    ),
                ),
            ],
            encoders: [
                new JsonEncoder(),
            ],
            defaultContext: $this->defaultContext,
        );
    }

    private function createClassMetadataFactory(): ClassMetadataFactoryInterface
    {
        return new ClassMetadataFactory(
            new LoaderChain([
                new AttributeLoader(),
            ]),
        );
    }

    private function createNameConverter(): NameConverterInterface
    {
        return new MetadataAwareNameConverter(
            $this->createClassMetadataFactory(),
            new CamelCaseToSnakeCaseNameConverter(),
        );
    }
}
