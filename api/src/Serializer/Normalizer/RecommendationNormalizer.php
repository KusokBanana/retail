<?php

namespace App\Serializer\Normalizer;

use App\Entity\Recommendation;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RecommendationNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, string $format = null, array $context = []): array
    {
        /* @var $object Recommendation */
        return [
            'id' => $object->getId(),
            'product' => $object->getProduct(),
            'status' => $object->getStatus(),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Recommendation;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
