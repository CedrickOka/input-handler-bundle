<?php

namespace Oka\InputHandlerBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\ProblemNormalizer as BaseProblemNormalizer;

/**
 *
 * @author Cedrick Oka Baidai <cedric.baidai@veone.net>
 *
 */
class ProblemNormalizer extends BaseProblemNormalizer
{
    public function normalize($object, $format = null, array $context = [])
    {
        $data = parent::normalize($object, $format, $context);
        
        $data['title'] = $object->getStatusText();
        $data['detail'] = $object->getMessage();

        return $data;
    }
}
