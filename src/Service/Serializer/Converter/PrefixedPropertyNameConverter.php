<?php

namespace App\Service\Serializer\Converter;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class PrefixedPropertyNameConverter implements NameConverterInterface
{

    /**
     * @var string
     */
    protected string $prefix = '';

    /**
     * PrefixedPropertyNameConverter constructor.
     * @param string $prefix
     */
    public function __construct(string $prefix)
    {
        $this->prefix = rtrim($prefix, '_') . '_';
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @inheritDoc
     */
    public function normalize(string $propertyName) : string
    {
        return !$this->is_prefixed($propertyName) ? $this->prefix . $propertyName : $propertyName;
    }

    /**
     * @inheritDoc
     */
    public function denormalize(string $propertyName) : string
    {
        return $this->is_prefixed($propertyName) ? substr($propertyName, strlen($this->prefix)) : $propertyName;
    }

    /**
     * Does $propertyName have the prefix?
     *
     * @param string $propertyName
     * @return bool
     */
    protected function is_prefixed(string $propertyName) : bool
    {
        return (substr($propertyName, 0, strlen($this->prefix)) === $this->prefix);
    }
}
