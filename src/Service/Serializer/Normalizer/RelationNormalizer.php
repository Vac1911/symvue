<?php

namespace App\Service\Serializer\Normalizer;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Serializer;

class RelationNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @var string
     */
    protected string $removalToken = '';

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    protected \Symfony\Component\PropertyAccess\PropertyAccessor $propAccessor;

    public function __construct(EntityManagerInterface $em)
    {

        $this->propAccessor = PropertyAccess::createPropertyAccessor();
        $this->em = $em;
    }

    public function setNormalizers($normalizers)
    {
        $this->normalizers = $normalizers;
    }

    public function makeSerializer()
    {
        return new Serializer($this->normalizers, []);
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
     * @return RelationNormalizer
     */
    public function setRemovalToken(string $removalToken): RelationNormalizer
    {
        $this->removalToken = $removalToken;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return ($this->isProxyEntity($data) && !$data->__isInitialized()) || ($this->isProxyCollection($data) && !$data->isInitialized());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return false;
        return $this->isEntity(new $type);
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return $this->removalToken ?? '__STOP__';
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotNormalizableValueException
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
//        dump($data);
//
//        if(is_array($data))
//        if (array_key_exists('author', $data)) {
//            $author = $this->makeSerializer()->denormalize($data['author'], User::class, $format, $context);
//        }

        return $data;
    }

    protected function isProxyCollection($var): bool
    {
        if (!is_object($var))
            return false;
        return strpos(\get_class($var), 'PersistentCollection') !== false;
    }

    protected function isProxyEntity($var): bool
    {
        if (!is_object($var))
            return false;
        return strpos(\get_class($var), 'Proxies') !== false;
    }

    protected function isEntity($var): bool
    {
        if (!is_object($var))
            return false;
        return strpos(\get_class($var), 'Entity') !== false;
    }

    protected function getAssociationMappings($var)
    {
        return $this->em->getClassMetadata($var)->associationMappings;
    }
}
