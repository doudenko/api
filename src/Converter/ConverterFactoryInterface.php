<?php

namespace Doudenko\Api\Converter;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

interface ConverterFactoryInterface
{
    public function create(): SerializerInterface & NormalizerInterface;
}
