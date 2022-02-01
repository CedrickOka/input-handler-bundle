<?php

namespace Oka\InputHandlerBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 *
 * @Annotation
 * @Target("METHOD")
 * @Attributes({
 *  @Attribute("formats", type="array", required=false),
 *  @Attribute("target", type="string", required=false),
 *  @Attribute("fields_alias", type="array", required=false),
 *  @Attribute("constraints", type="string", required=false),
 *  @Attribute("violation", type="array", required=false),
 *  @Attribute("can_be_empty", type="boolean", required=false),
 *  @Attribute("validation_disabled", type="boolean", required=false),
 *  @Attribute("enable_validation", type="boolean", required=false)
 * })
 */
final class RequestContent
{
    /**
     * Content type list
     * Available values are: `form`, `json`, `xml`.
     *
     * @var array
     */
    private $formats;

    /**
     * @var string
     */
    private $target;

    /**
     * @var array
     */
    private $fieldsAlias;

    /**
     * @var string
     */
    private $constraints;

    /**
     * @var array
     */
    private $violation;

    /**
     * @var bool
     */
    private $canBeEmpty;

    /**
     * @var bool
     */
    private $validationDisabled;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        $this->formats = $data['formats'] ?? [];
        $this->target = $data['target'] ?? null;
        $this->fieldsAlias = $data['fields_alias'] ?? [];
        $this->constraints = $data['constraints'] ?? null;
        $this->canBeEmpty = (bool) ($data['can_be_empty'] ?? false);
        $this->validationDisabled = (bool) ($data['validation_disabled'] ?? $data['enable_validation'] ?? false);
        $this->violation = array_merge([
            'message' => 'request.format.invalid',
            'domain' => 'OkaInputHandlerBundle',
            'parameters' => [],
        ], $data['violation'] ?? []);

        if (null === $this->target && null === $this->constraints && false === $this->validationDisabled) {
            throw new \InvalidArgumentException('You must define "target" or "constraints" attributes for each @RequestContent annotation while request validation is enabled.');
        }

        if ($diff = array_diff(array_keys($this->violation), ['message', 'parameters', 'domain'])) {
            throw new \InvalidArgumentException(sprintf('The following configuration are not supported "%s" for "violation" attribute for each @RequestContent annotation while request validation is enabled.', implode(', ', $diff)));
        }

        if (false === is_array($this->formats)) {
            $this->formats = [$this->formats];
        }
    }

    public function getFormats(): array
    {
        return $this->formats;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function getFieldsAlias(): array
    {
        return $this->fieldsAlias;
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

    public function getTargetAttributeName(): ?string
    {
        $attributeName = null;

        if (null !== $this->target && false !== ($pos = strripos($this->target, '\\'))) {
            $attributeName = strtolower(trim(substr($this->target, $pos + 1)));
        }

        return $attributeName;
    }
}
