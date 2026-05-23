<?php

namespace Oka\InputHandlerBundle\Annotation;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class RequestContent
{
    /**
     * @param array $formats the available values are: `form`, `json`, `xml`
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private array $formats = [],
        private ?string $constraints = null,
        private array $violation = [],
        private bool $canBeEmpty = false,
        private bool $validationDisabled = false,
    ) {
        $this->violation = array_merge([
            'message' => 'request.format.invalid',
            'domain' => 'OkaInputHandlerBundle',
            'parameters' => [],
        ], $violation);

        if (null === $this->constraints && false === $this->validationDisabled) {
            throw new \InvalidArgumentException('You must define "constraints" attributes for each @RequestContent annotation while request validation is enabled.');
        }

        if ($diff = array_diff(array_keys($this->violation), ['message', 'parameters', 'domain'])) {
            throw new \InvalidArgumentException(sprintf('The following configuration are not supported "%s" for "violation" attribute for each @RequestContent annotation while request validation is enabled.', implode(', ', $diff)));
        }
    }

    public function getFormats(): array
    {
        return $this->formats;
    }

    public function getConstraints(): ?string
    {
        return $this->constraints;
    }

    public function getViolation(): array
    {
        return $this->violation;
    }

    public function isCanBeEmpty(): bool
    {
        return $this->canBeEmpty;
    }

    public function isValidationDisabled(): bool
    {
        return $this->validationDisabled;
    }
}
