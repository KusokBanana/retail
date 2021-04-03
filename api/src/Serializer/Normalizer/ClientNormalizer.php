<?php

namespace App\Serializer\Normalizer;

use App\Entity\Client;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ClientNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, string $format = null, array $context = []): array
    {
        /* @var $object Client */
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'sex' => $object->getPhone(),
            'city' => $object->getBoughtProductsCount(),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Client;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
