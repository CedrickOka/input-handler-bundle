<?php

namespace Oka\InputHandlerBundle\Annotation;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class AccessControl
{
    private ?string $versionNumber = null;
    private ?string $versionOperator = null;

    public function __construct(private string $protocol, private string $version, private array $formats = [])
    {
        $this->versionNumber = ((int) preg_replace('#[^0-9]#', '', $version));
        $this->versionOperator = preg_replace('#[a-z0-9._-]#', '', $version) ?: '==';
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getVersionNumber(): int
    {
        return $this->versionNumber;
    }

    public function getVersionOperator(): string
    {
        return $this->versionOperator;
    }

    public function getFormats(): array
    {
        return $this->formats;
    }
}
