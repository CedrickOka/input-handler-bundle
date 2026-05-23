<?php

namespace Oka\InputHandlerBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class ProblemNormalizer implements NormalizerInterface
{
    public function __construct(private NormalizerInterface $problemNormalizer)
    {
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        return [
            ...$this->problemNormalizer->normalize($object, $format, $context),
            'title' => $object->getStatusText(),
            'detail' => $object->getMessage(),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $this->problemNormalizer->supportsNormalization($data, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->problemNormalizer->getSupportedTypes($format);
    }
}
