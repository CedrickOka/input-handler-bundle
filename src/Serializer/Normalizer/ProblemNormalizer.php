<?php
namespace Oka\InputHandlerBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\ProblemNormalizer as BaseProblemNormalizer;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProblemNormalizer extends BaseProblemNormalizer
{
    private $translator;
    
    public function __construct(TranslatorInterface $translator, bool $debug = false, array $defaultContext = [])
    {
        $this->debug = $debug;
        $this->defaultContext = $defaultContext + $this->defaultContext;
    }
    
    public function normalize($object, string $format = null, array $context = [])
    {
        
    }
}
