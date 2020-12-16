<?php

namespace App\Service\Serializer\Normalizer;

use App\Collections\Arrayable;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ExcludedNormalizer implements NormalizerInterface
{
    protected string $removalToken = '';
    protected array $excluded;

    public function __construct($exclude = [])
    {
        if ($exclude instanceof Arrayable)
            $exclude = $exclude->toArray();
        $this->excluded = $exclude;
    }

    /**
     * @return string
     */
    public function getRemovalToken(): string
    {
        return $this->removalToken;
    }

    /**
     * @param string $removalToken
     * @return ExcludedNormalizer
     */
    public function setRemovalToken(string $removalToken): ExcludedNormalizer
    {
        $this->removalToken = $removalToken;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        if (!count($this->excluded))
            return false;

        if ($data instanceof Arrayable)
            $data = $data->toArray();

        if (is_array($data))
            return collect(array_keys($data))->contains(fn($key) => in_array($key, $this->excluded, true));
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $norm = (object)$object->toArray();
        foreach($this->excluded as $exclude)
            unset($norm->$exclude);
        return $norm;
//        return $this->removalToken ?? '__STOP__';
    }

//    static function cast($object, $class)
//    {
//        return unserialize(
//            preg_replace(
//                '/^O:\d+:"[^"]++"/',
//                'O:'.strlen($class).':"'.$class.'"',
//                serialize($object)
//            )
//        );
//    }
}
