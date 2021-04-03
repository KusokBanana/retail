<?php

namespace App\Serializer\Normalizer\Result;

use App\Result\ClientSummaryResult;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ClientSummaryResultNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, string $format = null, array $context = []): array
    {
        /* @var $object ClientSummaryResult */
        return [
            'client' => $this->normalizer->normalize($object, $format, $context),
            'summary' => $object->getSummary(),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ClientSummaryResult;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
