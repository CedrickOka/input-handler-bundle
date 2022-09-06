<?php

namespace Oka\InputHandlerBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\ProblemNormalizer as BaseProblemNormalizer;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class ProblemNormalizer extends BaseProblemNormalizer
{
    /**
     * {@inheritDoc}
     *
     * @see \Symfony\Component\Serializer\Normalizer\ProblemNormalizer::normalize()
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = parent::normalize($object, $format, $context);

        $data['title'] = $object->getStatusText();
        $data['detail'] = $object->getMessage();

        return $data;
    }
}
